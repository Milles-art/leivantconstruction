<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$catalog = require $root.'/config/vant_products.php';
$outDir = $root.'/public/images/catalog-tools';
$sourcePath = $outDir.'/sources.json';
$acceptedLicenses = ['cc0', 'public domain', 'pd', 'cc by', 'cc-by', 'cc by-sa', 'cc-by-sa', 'cc-by-sa-4.0', 'cc-by-4.0'];

if (! is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}

$sources = is_file($sourcePath)
    ? json_decode((string) file_get_contents($sourcePath), true) ?: []
    : [];

$limit = isset($argv[1]) ? max(1, (int) $argv[1]) : count($catalog['products']);
$fallbackOnly = in_array('--fallback-only', $argv, true);
$replaceGenerated = in_array('--replace-generated', $argv, true) || in_array('--replace', $argv, true);
$replaceProvided = in_array('--replace-provided', $argv, true) || in_array('--replace', $argv, true);
$products = array_slice($catalog['products'], 0, $limit);

foreach ($products as $product) {
    $slug = $product['slug'];

    if (hasCatalogImage($outDir, $slug) && ! shouldReplaceExisting($sources, $slug, $replaceGenerated, $replaceProvided)) {
        $sources[$slug] ??= [
            'source' => 'generated-svg-placeholder',
            'license' => 'VANT generated fallback',
            'title' => $product['name'],
        ];
        ksort($sources);
        file_put_contents($sourcePath, json_encode($sources, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
        echo "skip existing {$slug}\n";
        continue;
    }

    $result = $fallbackOnly ? null : findSafeCommonsImage($product['name'], $product['category'], $acceptedLicenses);

    if (! $result) {
        echo "fallback {$slug}\n";
        writePlaceholder($outDir, $slug, $product['name'], $product['category']);
        $sources[$slug] = [
            'source' => 'generated-svg-placeholder',
            'license' => 'VANT generated fallback',
            'title' => $product['name'],
        ];
        ksort($sources);
        file_put_contents($sourcePath, json_encode($sources, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
        continue;
    }

    $extension = extensionFromContentType($result['content_type']) ?? extensionFromUrl($result['url']) ?? 'jpg';
    $path = "{$outDir}/{$slug}.{$extension}";
    file_put_contents($path, $result['bytes']);

    $sources[$slug] = [
        'source' => 'Wikimedia Commons',
        'license' => $result['license'],
        'title' => $result['title'],
        'url' => $result['page_url'],
        'image_url' => $result['url'],
        'attribution_required' => $result['attribution_required'],
    ];

    ksort($sources);
    file_put_contents($sourcePath, json_encode($sources, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
    echo "downloaded {$slug} from {$result['title']}\n";
}

ksort($sources);
file_put_contents($sourcePath, json_encode($sources, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);

function hasCatalogImage(string $outDir, string $slug): bool
{
    foreach (['jpg', 'jpeg', 'png', 'webp', 'svg'] as $extension) {
        if (is_file("{$outDir}/{$slug}.{$extension}")) {
            return true;
        }
    }

    return false;
}

function shouldReplaceExisting(array $sources, string $slug, bool $replaceGenerated, bool $replaceProvided): bool
{
    if (in_array('--replace', $GLOBALS['argv'] ?? [], true)) {
        return true;
    }

    $source = strtolower((string) ($sources[$slug]['source'] ?? ''));

    if ($replaceGenerated && $source === 'generated-svg-placeholder') {
        return true;
    }

    if ($replaceProvided && str_contains($source, 'generated')) {
        return true;
    }

    return false;
}

function findSafeCommonsImage(string $name, string $category, array $acceptedLicenses): ?array
{
    $queries = array_unique([
        "{$name} {$category}",
        "{$name} tool",
        $name,
    ]);

    foreach ($queries as $query) {
        $pages = commonsSearch($query);

        foreach ($pages as $page) {
            $info = $page['imageinfo'][0] ?? null;

            if (! $info) {
                continue;
            }

            $license = strtolower((string) ($info['extmetadata']['LicenseShortName']['value'] ?? ''));
            $attribution = strtolower((string) ($info['extmetadata']['AttributionRequired']['value'] ?? 'false'));

            if (! in_array($license, $acceptedLicenses, true)) {
                continue;
            }

            $url = $info['thumburl'] ?? $info['url'] ?? null;

            if (! $url || ! titleLooksRelevant($page['title'] ?? '', $name)) {
                continue;
            }

            $download = download($url);

            if (! $download || ! str_starts_with($download['content_type'], 'image/')) {
                continue;
            }

            return [
                'title' => $page['title'] ?? $name,
                'page_url' => $info['descriptionurl'] ?? '',
                'url' => $url,
                'license' => $info['extmetadata']['LicenseShortName']['value'] ?? 'Public Domain / CC0',
                'attribution_required' => $info['extmetadata']['AttributionRequired']['value'] ?? 'false',
                'content_type' => $download['content_type'],
                'bytes' => $download['bytes'],
            ];
        }
    }

    return null;
}

function commonsSearch(string $query): array
{
    $params = http_build_query([
        'action' => 'query',
        'generator' => 'search',
        'gsrnamespace' => 6,
        'gsrsearch' => $query,
        'gsrlimit' => 4,
        'prop' => 'imageinfo',
        'iiprop' => 'url|extmetadata|mime',
        'iiurlwidth' => 720,
        'format' => 'json',
        'origin' => '*',
    ]);

    $json = httpGet("https://commons.wikimedia.org/w/api.php?{$params}");
    $data = $json ? json_decode($json, true) : null;

    return array_values($data['query']['pages'] ?? []);
}

function titleLooksRelevant(string $title, string $name): bool
{
    $haystack = strtolower($title);
    $tokens = array_values(array_filter(
        preg_split('/[^a-z0-9]+/', strtolower($name)) ?: [],
        fn (string $token): bool => strlen($token) >= 4 && ! in_array($token, ['with', 'plus', 'small', 'large'], true)
    ));

    if ($tokens === []) {
        return true;
    }

    foreach ($tokens as $token) {
        if (str_contains($haystack, $token)) {
            return true;
        }
    }

    return false;
}

function download(string $url): ?array
{
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: VANTConstructionDemo/1.0\r\n",
            'timeout' => 8,
        ],
    ]);

    $bytes = @file_get_contents($url, false, $context);

    if ($bytes === false || strlen($bytes) < 1000) {
        return null;
    }

    $contentType = 'image/jpeg';

    foreach ($http_response_header ?? [] as $header) {
        if (stripos($header, 'Content-Type:') === 0) {
            $contentType = trim(substr($header, 13));
            break;
        }
    }

    return ['bytes' => $bytes, 'content_type' => strtolower(explode(';', $contentType)[0])];
}

function httpGet(string $url): ?string
{
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: VANTConstructionDemo/1.0\r\n",
            'timeout' => 6,
        ],
    ]);

    $result = @file_get_contents($url, false, $context);

    return $result === false ? null : $result;
}

function extensionFromContentType(string $contentType): ?string
{
    return match ($contentType) {
        'image/jpeg', 'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
        default => null,
    };
}

function extensionFromUrl(string $url): ?string
{
    $path = parse_url($url, PHP_URL_PATH) ?: '';
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true) ? $extension : null;
}

function writePlaceholder(string $outDir, string $slug, string $name, string $category): void
{
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safeCategory = htmlspecialchars($category, ENT_QUOTES, 'UTF-8');
    [$accent, $deep, $soft, $illustration] = productVisual($name, $category);
    $nameSvg = svgTextLines($name, 28, 112, 617, 42, '#ffffff', 1.05);

    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800" role="img" aria-label="{$safeName}">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#f8fafc"/>
      <stop offset="0.58" stop-color="#f1f5f9"/>
      <stop offset="1" stop-color="#e2e8f0"/>
    </linearGradient>
    <linearGradient id="accent" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$accent}"/>
      <stop offset="1" stop-color="#E87722"/>
    </linearGradient>
    <filter id="shadow" x="-20%" y="-20%" width="140%" height="150%">
      <feDropShadow dx="0" dy="24" stdDeviation="24" flood-color="#0f172a" flood-opacity=".22"/>
    </filter>
  </defs>
  <rect width="1200" height="800" fill="url(#bg)"/>
  <path d="M0 662 255 480l194 82 262-310 489 390v158H0z" fill="{$accent}" opacity=".10"/>
  <circle cx="978" cy="147" r="190" fill="{$soft}" opacity=".34"/>
  <circle cx="192" cy="179" r="114" fill="{$accent}" opacity=".11"/>
  <path d="M58 106h1084M58 236h1084M58 366h1084M58 496h1084M58 626h1084M178 54v690M350 54v690M522 54v690M694 54v690M866 54v690M1038 54v690" stroke="#0f172a" stroke-width="1.5" opacity=".055"/>

  <rect x="72" y="68" width="1056" height="664" rx="38" fill="#ffffff" stroke="#d4d4d8" stroke-width="3" filter="url(#shadow)"/>
  <rect x="94" y="90" width="1012" height="620" rx="28" fill="#f8fafc"/>
  <path d="M94 532h1012v178H94z" fill="#0a0a0a"/>
  <path d="M94 532h1012v10H94z" fill="url(#accent)"/>

  <g transform="translate(192 122)">
    <ellipse cx="408" cy="392" rx="375" ry="52" fill="#0f172a" opacity=".14"/>
    {$illustration}
  </g>

  <rect x="896" y="118" width="166" height="54" rx="12" fill="#0a0a0a"/>
  <text x="979" y="153" fill="#ffffff" text-anchor="middle" font-family="Arial, sans-serif" font-size="24" font-weight="900">VANT</text>
  <text x="112" y="583" fill="{$accent}" font-family="Arial, sans-serif" font-size="22" font-weight="900" letter-spacing="2">TOOLS &amp; EQUIPMENT</text>
  {$nameSvg}
  <text x="112" y="690" fill="#e5e7eb" font-family="Arial, sans-serif" font-size="24" font-weight="800">{$safeCategory}</text>
</svg>
SVG;

    file_put_contents("{$outDir}/{$slug}.svg", $svg);
}

function svgTextLines(string $text, int $maxChars, int $x, int $firstY, int $fontSize, string $color, float $lineHeight): string
{
    $words = preg_split('/\s+/', trim($text)) ?: [];
    $lines = [];
    $current = '';

    foreach ($words as $word) {
        $candidate = trim($current.' '.$word);
        if ($current !== '' && strlen($candidate) > $maxChars) {
            $lines[] = $current;
            $current = $word;
            continue;
        }
        $current = $candidate;
    }

    if ($current !== '') {
        $lines[] = $current;
    }

    $lines = array_slice($lines, 0, 2);
    $svg = '';

    foreach ($lines as $index => $line) {
        $safeLine = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
        $y = $firstY + (int) round($index * $fontSize * $lineHeight);
        $svg .= '<text x="'.$x.'" y="'.$y.'" fill="'.$color.'" font-family="Arial, sans-serif" font-size="'.$fontSize.'" font-weight="900">'.$safeLine.'</text>'."\n  ";
    }

    return $svg;
}

function productVisual(string $name, string $category): array
{
    [$accent, $deep, $soft] = categoryPalette($category);
    $key = strtolower($name.' '.$category);

    $visual = match (true) {
        hasAny($key, ['excavator', 'backhoe']) => excavatorVisual($accent, $deep),
        hasAny($key, ['bulldozer']) => bulldozerVisual($accent, $deep),
        hasAny($key, ['wheel loader', 'skid steer', 'loader']) => loaderVisual($accent, $deep),
        hasAny($key, ['motor grader']) => graderVisual($accent, $deep),
        hasAny($key, ['dump truck', 'hauler', 'truck']) => truckVisual($accent, $deep),
        hasAny($key, ['roller', 'compactor']) => rollerVisual($accent, $deep),
        hasAny($key, ['trencher']) => trencherVisual($accent, $deep),
        hasAny($key, ['mixer']) => mixerVisual($accent, $deep),
        hasAny($key, ['concrete pump', 'pump truck']) => concretePumpVisual($accent, $deep),
        hasAny($key, ['batch plant']) => batchPlantVisual($accent, $deep),
        hasAny($key, ['tower crane', 'mobile crane', 'crane']) => craneVisual($accent, $deep),
        hasAny($key, ['forklift']) => forkliftVisual($accent, $deep),
        hasAny($key, ['manlift', 'boom lift', 'scissor lift']) => accessLiftVisual($accent, $deep),
        hasAny($key, ['scaffolding']) => scaffoldingVisual($accent, $deep),
        hasAny($key, ['ladder']) => ladderVisual($accent, $deep),
        hasAny($key, ['drill rig', 'pile boring', 'pile driver', 'auger']) => drillingVisual($accent, $deep),
        hasAny($key, ['rock breaker']) => breakerVisual($accent, $deep),
        hasAny($key, ['laser level', 'total station', 'theodolite', 'gps survey']) => surveyVisual($accent, $deep),
        hasAny($key, ['generator', 'ups system']) => generatorVisual($accent, $deep),
        hasAny($key, ['light tower', 'floodlight']) => lightVisual($accent, $deep),
        hasAny($key, ['multimeter', 'voltage tester', 'extension reels', 'power strips']) => electricalVisual($accent, $deep),
        hasAny($key, ['helmet', 'hard hat']) => helmetVisual($accent, $deep),
        hasAny($key, ['boots']) => bootsVisual($accent, $deep),
        hasAny($key, ['vest']) => vestVisual($accent, $deep),
        hasAny($key, ['gloves']) => glovesVisual($accent, $deep),
        hasAny($key, ['goggles', 'face shield', 'ear muffs', 'earplugs', 'mask', 'respirator']) => ppeVisual($accent, $deep),
        hasAny($key, ['harness']) => harnessVisual($accent, $deep),
        hasAny($key, ['first aid']) => firstAidVisual($accent, $deep),
        hasAny($key, ['fire extinguisher']) => extinguisherVisual($accent, $deep),
        hasAny($key, ['submersible pump', 'wellpoint', 'centrifugal pump', 'dewatering']) => waterPumpVisual($accent, $deep),
        hasAny($key, ['barrier', 'barricade']) => barrierVisual($accent, $deep),
        hasAny($key, ['cement bags']) => cementBagVisual($accent, $deep),
        hasAny($key, ['welding']) => weldingVisual($accent, $deep),
        hasAny($key, ['paint sprayer']) => sprayerVisual($accent, $deep),
        hasAny($key, ['toolbox', 'storage cabinet']) => toolboxVisual($accent, $deep),
        hasAny($key, ['drill', 'impact driver', 'rotary hammer', 'nail gun']) => powerDrillVisual($accent, $deep),
        hasAny($key, ['angle grinder', 'bench grinder']) => grinderVisual($accent, $deep),
        hasAny($key, ['circular saw', 'reciprocating saw', 'jigsaw', 'hand saw', 'hacksaw']) => sawVisual($accent, $deep),
        hasAny($key, ['sander']) => sanderVisual($accent, $deep),
        hasAny($key, ['trowel', 'float', 'edger', 'groover', 'bump cutter']) => trowelVisual($accent, $deep),
        hasAny($key, ['wheelbarrow']) => wheelbarrowVisual($accent, $deep),
        hasAny($key, ['shovel', 'spade']) => shovelVisual($accent, $deep),
        hasAny($key, ['pickaxe', 'digging bar']) => pickaxeVisual($accent, $deep),
        hasAny($key, ['wrench', 'pliers', 'screwdriver', 'chisel', 'tape measure', 'spirit level', 'caulk gun']) => handToolSetVisual($accent, $deep),
        hasAny($key, ['hammer', 'sledgehammer']) => hammerVisual($accent, $deep),
        default => categoryFallbackVisual($category, $accent, $deep),
    };

    return [$accent, $deep, $soft, $visual];
}

function hasAny(string $haystack, array $needles): bool
{
    foreach ($needles as $needle) {
        if (str_contains($haystack, $needle)) {
            return true;
        }
    }

    return false;
}

function categoryPalette(string $category): array
{
    return match ($category) {
        'Power Tools', 'Surveying & Measuring', 'Dewatering Equipment' => ['#2563EB', '#0f172a', '#dbeafe'],
        'Earthmoving & Heavy Machinery', 'Concrete & Masonry Equipment', 'Lifting & Material Handling' => ['#E87722', '#111827', '#ffedd5'],
        'Electrical & Power Equipment', 'Safety & PPE' => ['#2F855A', '#0f172a', '#dcfce7'],
        'Foundation & Drilling Equipment' => ['#475569', '#0f172a', '#e2e8f0'],
        default => ['#D4A017', '#111827', '#fef3c7'],
    };
}

function svgStroke(string $accent, string $deep, string $body): string
{
    return <<<SVG
<g fill="none" stroke-linecap="round" stroke-linejoin="round">
  <g stroke="{$deep}" stroke-width="26">{$body}</g>
  <circle cx="502" cy="324" r="18" fill="{$accent}" stroke="none"/>
</g>
SVG;
}

function excavatorVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M96 298h372c38 0 70 31 70 70H94c0-39 2-70 2-70Z" fill="{$deep}"/>
  <circle cx="178" cy="368" r="45" fill="#f8fafc" stroke="{$deep}" stroke-width="24"/>
  <circle cx="378" cy="368" r="45" fill="#f8fafc" stroke="{$deep}" stroke-width="24"/>
  <path d="M148 210h198l73 88H118l30-88Z" fill="{$accent}"/>
  <path d="M216 230h84l38 48H202l14-48Z" fill="#ffffff" opacity=".9"/>
  <path d="M392 229 510 96l44 32-96 136" fill="none" stroke="{$deep}" stroke-width="30" stroke-linecap="round"/>
  <path d="M552 126 642 82l28 48-98 38Z" fill="{$accent}"/>
</g>
SVG;
}

function bulldozerVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <rect x="82" y="292" width="438" height="86" rx="43" fill="{$deep}"/>
  <circle cx="170" cy="335" r="28" fill="#ffffff"/>
  <circle cx="274" cy="335" r="28" fill="#ffffff"/>
  <circle cx="378" cy="335" r="28" fill="#ffffff"/>
  <path d="M138 202h230l54 90H108l30-90Z" fill="{$accent}"/>
  <path d="M209 219h88l36 54H190l19-54Z" fill="#ffffff" opacity=".9"/>
  <path d="M524 260h82l64 132H524z" fill="{$accent}"/>
</g>
SVG;
}

function loaderVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <circle cx="178" cy="350" r="58" fill="#ffffff" stroke="{$deep}" stroke-width="26"/>
  <circle cx="420" cy="350" r="58" fill="#ffffff" stroke="{$deep}" stroke-width="26"/>
  <path d="M122 240h272l74 86H92l30-86Z" fill="{$accent}"/>
  <path d="M204 256h94l38 50H188l16-50Z" fill="#ffffff" opacity=".9"/>
  <path d="M486 260 645 205l30 76-164 45Z" fill="{$deep}"/>
  <path d="M618 194h82l-42 132h-106Z" fill="{$accent}"/>
</g>
SVG;
}

function graderVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <circle cx="128" cy="354" r="43" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <circle cx="540" cy="354" r="48" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <path d="M110 252h470l52 74H92l18-74Z" fill="{$accent}"/>
  <path d="M224 280h196" stroke="{$deep}" stroke-width="28" stroke-linecap="round"/>
  <path d="M250 364h270l-70 50H188z" fill="{$deep}"/>
</g>
SVG;
}

function truckVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M82 236h342l38 100H82z" fill="{$accent}"/>
  <path d="M462 260h112l72 76H462z" fill="{$deep}"/>
  <circle cx="188" cy="354" r="53" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <circle cx="520" cy="354" r="53" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <path d="M500 274h50l35 38h-85z" fill="#ffffff" opacity=".9"/>
</g>
SVG;
}

function rollerVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <circle cx="184" cy="340" r="88" fill="{$accent}" stroke="{$deep}" stroke-width="26"/>
  <circle cx="502" cy="350" r="60" fill="#ffffff" stroke="{$deep}" stroke-width="26"/>
  <path d="M272 256h238l62 74H270z" fill="{$accent}"/>
  <path d="M340 210h120l50 46H306z" fill="{$deep}"/>
</g>
SVG;
}

function trencherVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M120 254h272l70 78H90z" fill="{$accent}"/>
  <circle cx="180" cy="356" r="48" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <circle cx="360" cy="356" r="48" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <path d="M450 258h190l-70 138H440z" fill="{$deep}"/>
  <path d="M492 285h98M474 326h92M456 367h82" stroke="#ffffff" stroke-width="18" stroke-linecap="round" opacity=".8"/>
</g>
SVG;
}

function mixerVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M206 177h236l-48 158H154z" fill="{$accent}"/>
  <path d="M214 177c25-58 178-58 206 0" fill="none" stroke="{$deep}" stroke-width="30" stroke-linecap="round"/>
  <path d="M178 335h264" stroke="{$deep}" stroke-width="28" stroke-linecap="round"/>
  <circle cx="196" cy="384" r="40" fill="#ffffff" stroke="{$deep}" stroke-width="22"/>
  <circle cx="402" cy="384" r="40" fill="#ffffff" stroke="{$deep}" stroke-width="22"/>
  <path d="M448 219h132v46H448z" fill="{$deep}"/>
</g>
SVG;
}

function concretePumpVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M80 282h354l56 68H80z" fill="{$accent}"/>
  <circle cx="168" cy="360" r="46" fill="#ffffff" stroke="{$deep}" stroke-width="23"/>
  <circle cx="408" cy="360" r="46" fill="#ffffff" stroke="{$deep}" stroke-width="23"/>
  <path d="M300 270 C370 154 476 110 620 136" fill="none" stroke="{$deep}" stroke-width="26" stroke-linecap="round"/>
  <path d="M618 136c40 10 60 34 60 72" fill="none" stroke="{$accent}" stroke-width="24" stroke-linecap="round"/>
</g>
SVG;
}

function batchPlantVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M128 116h128l40 180H88z" fill="{$accent}"/>
  <path d="M350 116h128l46 180H312z" fill="{$deep}"/>
  <path d="M110 296h430" stroke="{$deep}" stroke-width="28" stroke-linecap="round"/>
  <path d="M164 296v104M456 296v104M232 296l-78 104M388 296l78 104" stroke="{$deep}" stroke-width="22" stroke-linecap="round"/>
</g>
SVG;
}

function craneVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M155 404V82h94"/><path d="M249 82h356"/><path d="M545 82v132"/><path d="M505 214h82"/><path d="M545 214v96"/><path d="M505 310h82"/><path d="M155 144h290"/><path d="M155 226h216"/><path d="M95 404h210"/>');
}

function forkliftVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M120 250h248l72 86H90z" fill="{$accent}"/>
  <circle cx="166" cy="356" r="48" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <circle cx="394" cy="356" r="48" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <path d="M492 128v232M534 128v232" stroke="{$deep}" stroke-width="26" stroke-linecap="round"/>
  <path d="M486 340h168M486 382h210" stroke="{$deep}" stroke-width="22" stroke-linecap="round"/>
</g>
SVG;
}

function accessLiftVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M128 392h430"/><path d="M216 392l236-236"/><path d="M452 392 216 156"/><path d="M250 260h170"/><path d="M350 156h170v78H350z"/><path d="M166 96h128v60H166z"/>');
}

function scaffoldingVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M120 92v318M280 92v318M440 92v318M600 92v318"/><path d="M92 130h536M92 246h536M92 362h536"/><path d="M120 130l160 116M280 130 120 246M280 246l160 116M440 246 280 362M440 130l160 116M600 130 440 246"/>');
}

function ladderVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M238 82 104 410"/><path d="M448 82 582 410"/><path d="M204 160h276"/><path d="M174 236h336"/><path d="M144 312h396"/><path d="M112 388h458"/>');
}

function drillingVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M350 60v354"/><path d="M250 118h200"/><path d="M280 198h140"/><path d="M310 278h80"/><path d="M350 414l-70-96"/><path d="M350 414l70-96"/><path d="M120 414h460"/><path d="M350 60l42 58M350 60l-42 58"/>');
}

function breakerVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M310 70h118v188H310z"/><path d="M354 258v104"/><path d="M326 362h86"/><path d="M238 136h72"/><path d="M428 136h72"/><path d="M178 414h330"/><path d="M220 414l-40 46"/><path d="M320 414l-32 58"/><path d="M440 414l44 46"/>');
}

function surveyVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M274 95h166v116H274z"/><path d="M356 211v84"/><path d="M356 295 210 430"/><path d="M356 295l146 135"/><path d="M356 295v135"/><path d="M112 150h118"/><path d="M171 94v112"/><path d="M440 153h190"/>');
}

function generatorVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <rect x="120" y="128" width="450" height="248" rx="28" fill="{$accent}" stroke="{$deep}" stroke-width="28"/>
  <circle cx="256" cy="252" r="62" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <path d="M378 212h112M378 274h112" stroke="{$deep}" stroke-width="24" stroke-linecap="round"/>
  <path d="M180 376v48M510 376v48" stroke="{$deep}" stroke-width="24" stroke-linecap="round"/>
</g>
SVG;
}

function lightVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M350 404V142"/><path d="M234 404h232"/><path d="M266 142h168"/><path d="M230 98h240v88H230z"/><path d="M350 186 236 404"/><path d="M350 186l114 218"/>');
}

function electricalVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<rect x="180" y="100" width="320" height="290" rx="34"/><path d="M314 170h88l-58 82h84l-118 104 42-84h-86z"/><path d="M106 218h74"/><path d="M500 218h74"/><path d="M244 390v54M436 390v54"/>');
}

function helmetVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M150 250c35-118 120-176 255-176s220 58 255 176"/><path d="M128 250h554"/><path d="M194 250v72c0 98 86 178 211 178s211-80 211-178v-72"/><path d="M286 144v106"/><path d="M524 144v106"/>');
}

function bootsVisual(string $accent, string $deep): string
{
    return <<<SVG
<g fill="{$accent}" stroke="{$deep}" stroke-width="24" stroke-linejoin="round">
  <path d="M176 122h150v218h116c42 0 76 34 76 76H176z"/>
  <path d="M442 170h122v170h74c42 0 76 34 76 76H442z"/>
</g>
SVG;
}

function vestVisual(string $accent, string $deep): string
{
    return <<<SVG
<g fill="{$accent}" stroke="{$deep}" stroke-width="24" stroke-linejoin="round">
  <path d="M230 90h112l58 94 58-94h112l86 324H144z"/>
  <path d="M276 170v244M524 170v244M180 286h440" fill="none"/>
</g>
SVG;
}

function glovesVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M258 132v170"/><path d="M318 96v206"/><path d="M378 118v184"/><path d="M438 168v134"/><path d="M210 214l-48 74c-32 50 4 116 64 116h230c58 0 104-46 104-104v-94"/><path d="M520 170l86 56"/>');
}

function ppeVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M160 230h180c36 0 66 30 66 66s-30 66-66 66H160z"/><path d="M406 296h62"/><path d="M468 230h180v132H468z"/><path d="M204 164h400"/><path d="M254 164v66M552 164v66"/>');
}

function harnessVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M250 94 400 404"/><path d="M550 94 400 404"/><path d="M280 170h240"/><path d="M228 294h344"/><circle cx="400" cy="404" r="46"/><path d="M205 94h390"/>');
}

function firstAidVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <rect x="154" y="156" width="492" height="282" rx="36" fill="{$accent}" stroke="{$deep}" stroke-width="28"/>
  <path d="M330 100h140v56H330z" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <path d="M400 218v158M321 297h158" stroke="#ffffff" stroke-width="42" stroke-linecap="round"/>
</g>
SVG;
}

function extinguisherVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M312 176h176v260c0 48-39 86-88 86s-88-38-88-86z"/><path d="M350 106h100v70H350z"/><path d="M450 126h122"/><path d="M572 126v80"/><path d="M342 270h116"/><path d="M342 342h116"/>');
}

function waterPumpVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M164 174h282c76 0 138 62 138 138s-62 138-138 138H164z" fill="{$accent}" stroke="{$deep}" stroke-width="26"/>
  <circle cx="438" cy="312" r="78" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
  <path d="M72 254h104M72 370h104M564 240h100M564 384h100" stroke="{$deep}" stroke-width="26" stroke-linecap="round"/>
  <path d="M150 496c64-34 128 34 192 0s128 34 192 0" fill="none" stroke="#2563EB" stroke-width="20" stroke-linecap="round"/>
</g>
SVG;
}

function barrierVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M110 164h540v164H110z"/><path d="M110 246h540"/><path d="M186 164v164M302 164v164M418 164v164M534 164v164"/><path d="M160 328v92M600 328v92"/><path d="M110 164l94-74"/><path d="M650 164l-94-74"/>');
}

function cementBagVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M182 136h420l52 274c6 32-18 62-51 62H181c-33 0-57-30-51-62z" fill="{$accent}" stroke="{$deep}" stroke-width="28"/>
  <path d="M226 240h330M250 320h282" stroke="#ffffff" stroke-width="30" stroke-linecap="round" opacity=".9"/>
  <text x="392" y="386" fill="#ffffff" text-anchor="middle" font-family="Arial, sans-serif" font-size="58" font-weight="900">CEMENT</text>
</g>
SVG;
}

function weldingVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<rect x="154" y="160" width="260" height="190" rx="24"/><path d="M414 254h120"/><path d="M534 254l80 80"/><path d="M610 112l28 70M686 192l-70 28M638 232l58 54"/><path d="M210 350v70M358 350v70"/>');
}

function sprayerVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M144 194h280l82 64v72H214l-70-72z"/><path d="M424 218h122"/><path d="M546 218l84-60"/><path d="M274 330v90h104v-90"/><path d="M626 158l58-42M646 210l80-14M630 264l72 30"/>');
}

function toolboxVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <rect x="126" y="190" width="548" height="264" rx="30" fill="{$accent}" stroke="{$deep}" stroke-width="28"/>
  <path d="M290 190v-58h220v58M126 278h548M350 278v74h100v-74" fill="none" stroke="{$deep}" stroke-width="26" stroke-linejoin="round"/>
</g>
SVG;
}

function powerDrillVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M120 196h324l88 74v86H208l-88-86z" fill="{$accent}" stroke="{$deep}" stroke-width="28" stroke-linejoin="round"/>
  <path d="M444 212h110l84 58" fill="none" stroke="{$deep}" stroke-width="26" stroke-linecap="round"/>
  <path d="M268 356v104h116V356" fill="{$accent}" stroke="{$deep}" stroke-width="26" stroke-linejoin="round"/>
  <path d="M638 270h104" stroke="{$deep}" stroke-width="26" stroke-linecap="round"/>
  <path d="M220 250h126" stroke="#ffffff" stroke-width="30" stroke-linecap="round" opacity=".85"/>
</g>
SVG;
}

function grinderVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <circle cx="506" cy="292" r="112" fill="#ffffff" stroke="{$deep}" stroke-width="28"/>
  <circle cx="506" cy="292" r="44" fill="{$accent}"/>
  <path d="M140 230h316l46 92H212z" fill="{$accent}" stroke="{$deep}" stroke-width="26" stroke-linejoin="round"/>
  <path d="M206 322l-68 96" stroke="{$deep}" stroke-width="28" stroke-linecap="round"/>
</g>
SVG;
}

function sawVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <circle cx="422" cy="300" r="128" fill="#ffffff" stroke="{$deep}" stroke-width="28"/>
  <path d="M422 172 446 248l82-52-52 82 88 22-88 22 52 82-82-52-24 76-24-76-82 52 52-82-88-22 88-22-52-82 82 52z" fill="{$accent}"/>
  <path d="M128 228h250l62 72H128z" fill="{$accent}" stroke="{$deep}" stroke-width="26" stroke-linejoin="round"/>
</g>
SVG;
}

function sanderVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M146 236h430l78 104H104z" fill="{$accent}" stroke="{$deep}" stroke-width="28" stroke-linejoin="round"/>
  <path d="M260 164h220l54 72H212z" fill="#ffffff" stroke="{$deep}" stroke-width="26"/>
  <path d="M154 374h470" stroke="{$deep}" stroke-width="32" stroke-linecap="round"/>
</g>
SVG;
}

function trowelVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M130 300h408l-130 104H196z"/><path d="M314 300l170-170"/><path d="M454 112l86 86"/><path d="M530 188l78-78"/><path d="M116 404h420"/>');
}

function wheelbarrowVisual(string $accent, string $deep): string
{
    return <<<SVG
<g>
  <path d="M126 182h418l-82 166H196z" fill="{$accent}" stroke="{$deep}" stroke-width="28" stroke-linejoin="round"/>
  <path d="M462 348h136M170 348l-88 68" stroke="{$deep}" stroke-width="28" stroke-linecap="round"/>
  <circle cx="358" cy="398" r="52" fill="#ffffff" stroke="{$deep}" stroke-width="24"/>
</g>
SVG;
}

function shovelVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M426 78 240 264"/><path d="M194 264h92l36 98-82 82-82-82z"/><path d="M426 78l64-64 64 64-64 64z"/><path d="M134 444h214"/>');
}

function pickaxeVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M392 104 212 408"/><path d="M210 132c126-76 252-76 378 0"/><path d="M210 132h-76"/><path d="M588 132h76"/><path d="M158 408h208"/>');
}

function handToolSetVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M128 374 384 118"/><path d="M420 96l86 86"/><path d="M190 148l252 252"/><path d="M122 124l86-86"/><path d="M512 312l96 96"/><path d="M608 408l72-72"/><path d="M92 408h244"/>');
}

function hammerVisual(string $accent, string $deep): string
{
    return svgStroke($accent, $deep, '<path d="M162 390 424 128"/><path d="M454 98l126 126"/><path d="M398 154l96-96c46-46 120-46 166 0l22 22-132 132"/><path d="M122 430l96 96"/>');
}

function categoryFallbackVisual(string $category, string $accent, string $deep): string
{
    return match ($category) {
        'Power Tools' => powerDrillVisual($accent, $deep),
        'Earthmoving & Heavy Machinery' => excavatorVisual($accent, $deep),
        'Concrete & Masonry Equipment' => mixerVisual($accent, $deep),
        'Lifting & Material Handling' => craneVisual($accent, $deep),
        'Foundation & Drilling Equipment' => drillingVisual($accent, $deep),
        'Surveying & Measuring' => surveyVisual($accent, $deep),
        'Electrical & Power Equipment' => generatorVisual($accent, $deep),
        'Safety & PPE' => helmetVisual($accent, $deep),
        'Dewatering Equipment' => waterPumpVisual($accent, $deep),
        'Site Accessories & Consumables' => toolboxVisual($accent, $deep),
        default => hammerVisual($accent, $deep),
    };
}
