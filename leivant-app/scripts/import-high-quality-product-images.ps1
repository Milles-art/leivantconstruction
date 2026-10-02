param(
    [string] $SourceDir = '',
    [int] $Quality = 94
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$source = if ($SourceDir) { $SourceDir } else { Join-Path $root 'public\images\product-originals' }
$target = Join-Path $root 'public\images\catalog-tools'
$cwebp = Join-Path $root '.runtime\tools\libwebp-1.6.0-windows-x64\bin\cwebp.exe'

New-Item -ItemType Directory -Force -Path $source | Out-Null
New-Item -ItemType Directory -Force -Path $target | Out-Null

if (!(Test-Path -LiteralPath $cwebp)) {
    throw "cwebp.exe was not found at $cwebp"
}

$allowed = @('.png', '.jpg', '.jpeg', '.webp')
$images = Get-ChildItem -Path $source -File |
    Where-Object { $_.Extension.ToLowerInvariant() -in $allowed }

if ($images.Count -eq 0) {
    "No images found in $source"
    "Add high-resolution product images named by product slug, for example: claw-hammer.png, electric-drill.jpg, portable-concrete-mixer-350l.png"
    exit 0
}

$imported = 0

foreach ($image in $images) {
    $destination = Join-Path $target ($image.BaseName + '.webp')

    if ($image.Extension.ToLowerInvariant() -eq '.webp') {
        Copy-Item -LiteralPath $image.FullName -Destination $destination -Force
    } else {
        & $cwebp -quiet -q $Quality -m 6 -sharp_yuv $image.FullName -o $destination

        if ($LASTEXITCODE -ne 0 -or !(Test-Path -LiteralPath $destination)) {
            throw "Failed to convert $($image.Name) to WebP"
        }
    }

    $imported++
}

"imported: $imported"
"source: $source"
"target: $target"
"next: .\.runtime\php\php.exe artisan migrate --seed --force"

