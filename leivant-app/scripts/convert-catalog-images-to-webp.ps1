param(
    [int] $Quality = 88,
    [string] $SourceDir = '',
    [string] $TargetDir = '',
    [switch] $Replace
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$sourceDir = if ($SourceDir) { $SourceDir } else { Join-Path $root 'public\images\catalog-tools' }
$targetDir = if ($TargetDir) { $TargetDir } else { $sourceDir }
$cwebp = Join-Path $root '.runtime\tools\libwebp-1.6.0-windows-x64\bin\cwebp.exe'

if (!(Test-Path -LiteralPath $cwebp)) {
    throw "cwebp.exe was not found at $cwebp"
}

New-Item -ItemType Directory -Force -Path $targetDir | Out-Null

$images = Get-ChildItem -Path $sourceDir -File |
    Where-Object { $_.Extension.ToLowerInvariant() -in @('.png', '.jpg', '.jpeg') }

$converted = 0
$skipped = 0

foreach ($image in $images) {
    $target = Join-Path $targetDir ($image.BaseName + '.webp')

    if ((Test-Path -LiteralPath $target) -and -not $Replace) {
        $skipped++
        continue
    }

    & $cwebp -quiet -q $Quality -m 6 -sharp_yuv $image.FullName -o $target

    if ($LASTEXITCODE -ne 0 -or !(Test-Path -LiteralPath $target)) {
        throw "Failed to convert $($image.Name) to WebP"
    }

    $converted++
}

"converted: $converted"
"skipped: $skipped"
