param(
    [string] $Source = "C:\Users\Esrom Mussa\Downloads\ChatGPT Image May 9, 2026, 01_58_08 PM.png",
    [switch] $Replace
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$outDir = Join-Path $root 'public\images\catalog-tools'
New-Item -ItemType Directory -Force -Path $outDir | Out-Null

Add-Type -AssemblyName System.Drawing

function New-Cell {
    param(
        [string] $Slug,
        [int] $Col,
        [int] $Row,
        [int] $ColSpan = 1,
        [int] $RowSpan = 1
    )

    [pscustomobject]@{
        Slug = $Slug
        Col = $Col
        Row = $Row
        ColSpan = $ColSpan
        RowSpan = $RowSpan
    }
}

function Add-SectionCells {
    param(
        [System.Collections.Generic.List[object]] $Cells,
        [int] $X,
        [int] $Y,
        [int] $W,
        [int] $H,
        [int] $HeaderH,
        [int] $Cols,
        [int] $Rows,
        [array] $Items
    )

    $cellW = $W / $Cols
    $cellH = ($H - $HeaderH) / $Rows
    $gridY = $Y + $HeaderH

    foreach ($item in $Items) {
        $cropX = [int] [Math]::Round($X + ($item.Col * $cellW) + 4)
        $cropY = [int] [Math]::Round($gridY + ($item.Row * $cellH) + 4)
        $cropW = [int] [Math]::Round(($cellW * $item.ColSpan) - 8)
        $cropH = [int] [Math]::Round(($cellH * $item.RowSpan) - 24)

        $Cells.Add([pscustomobject]@{
            Slug = $item.Slug
            X = $cropX
            Y = $cropY
            W = $cropW
            H = $cropH
        })
    }
}

function Get-ContentBounds {
    param([System.Drawing.Bitmap] $Image)

    $minX = $Image.Width
    $minY = $Image.Height
    $maxX = -1
    $maxY = -1

    for ($y = 0; $y -lt $Image.Height; $y++) {
        for ($x = 0; $x -lt $Image.Width; $x++) {
            $pixel = $Image.GetPixel($x, $y)

            if ($pixel.R -lt 232 -or $pixel.G -lt 232 -or $pixel.B -lt 232) {
                if ($x -lt $minX) { $minX = $x }
                if ($y -lt $minY) { $minY = $y }
                if ($x -gt $maxX) { $maxX = $x }
                if ($y -gt $maxY) { $maxY = $y }
            }
        }
    }

    if ($maxX -lt 0 -or $maxY -lt 0) {
        return New-Object System.Drawing.Rectangle 0, 0, $Image.Width, $Image.Height
    }

    $pad = 6
    $x = [Math]::Max(0, $minX - $pad)
    $y = [Math]::Max(0, $minY - $pad)
    $right = [Math]::Min($Image.Width - 1, $maxX + $pad)
    $bottom = [Math]::Min($Image.Height - 1, $maxY + $pad)

    return New-Object System.Drawing.Rectangle $x, $y, ($right - $x + 1), ($bottom - $y + 1)
}

function Export-ToolImage {
    param(
        [System.Drawing.Bitmap] $Sheet,
        [object] $Cell
    )

    $target = Join-Path $outDir "$($Cell.Slug).png"

    if ($Cell.Slug -eq 'claw-hammer' -and (Test-Path -LiteralPath $target) -and (Get-Item -LiteralPath $target).Length -gt 250000) {
        return "skip $($Cell.Slug)"
    }

    if ((Test-Path -LiteralPath $target) -and -not $Replace) {
        return "skip $($Cell.Slug)"
    }

    $sourceRect = New-Object System.Drawing.Rectangle $Cell.X, $Cell.Y, $Cell.W, $Cell.H
    $rawCrop = $Sheet.Clone($sourceRect, $Sheet.PixelFormat)
    $bounds = Get-ContentBounds $rawCrop
    $crop = $rawCrop.Clone($bounds, $rawCrop.PixelFormat)

    $canvas = New-Object System.Drawing.Bitmap 900, 650
    $graphics = [System.Drawing.Graphics]::FromImage($canvas)
    $graphics.Clear([System.Drawing.Color]::White)
    $graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
    $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $graphics.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality

    $maxW = 760
    $maxH = 470
    $scale = [Math]::Min($maxW / $crop.Width, $maxH / $crop.Height)
    $drawW = [int] [Math]::Round($crop.Width * $scale)
    $drawH = [int] [Math]::Round($crop.Height * $scale)
    $drawX = [int] [Math]::Round((900 - $drawW) / 2)
    $drawY = [int] [Math]::Round((650 - $drawH) / 2)

    $graphics.DrawImage($crop, $drawX, $drawY, $drawW, $drawH)
    $canvas.Save($target, [System.Drawing.Imaging.ImageFormat]::Png)

    $graphics.Dispose()
    $canvas.Dispose()
    $crop.Dispose()
    $rawCrop.Dispose()

    return "import $($Cell.Slug)"
}

$cells = [System.Collections.Generic.List[object]]::new()

Add-SectionCells $cells 5 8 503 434 28 5 4 @(
    (New-Cell 'claw-hammer' 0 0), (New-Cell 'sledgehammer' 1 0), (New-Cell 'chisel-set' 2 0), (New-Cell 'screwdriver-set' 3 0), (New-Cell 'tape-measure-30ft' 4 0),
    (New-Cell 'spirit-level' 0 1), (New-Cell 'pliers-set' 1 1), (New-Cell 'wrench-set' 2 1), (New-Cell 'hand-saw' 3 1), (New-Cell 'hacksaw' 4 1),
    (New-Cell 'masonry-trowel' 0 2), (New-Cell 'plastering-trowel' 1 2), (New-Cell 'hawk-board' 2 2), (New-Cell 'concrete-float' 3 2), (New-Cell 'bull-float' 4 2),
    (New-Cell 'digging-bar' 0 3), (New-Cell 'pickaxe' 1 3), (New-Cell 'shovel-spade' 2 3), (New-Cell 'heavy-duty-wheelbarrow' 3 3), (New-Cell 'caulk-gun' 4 3)
)

Add-SectionCells $cells 514 8 505 319 28 5 3 @(
    (New-Cell 'electric-drill' 0 0), (New-Cell 'impact-driver' 1 0), (New-Cell 'rotary-hammer-drill' 2 0), (New-Cell 'circular-saw' 3 0), (New-Cell 'professional-angle-grinder' 4 0),
    (New-Cell 'reciprocating-saw' 0 1), (New-Cell 'jigsaw' 1 1), (New-Cell 'belt-sander' 2 1), (New-Cell 'concrete-vibrator-38mm' 3 1), (New-Cell 'power-trowel' 4 1),
    (New-Cell 'bench-grinder' 0 2), (New-Cell 'nail-gun' 1 2)
)

Add-SectionCells $cells 514 335 505 279 28 5 3 @(
    (New-Cell 'crawler-excavator' 0 0), (New-Cell 'wheeled-excavator' 1 0), (New-Cell 'bulldozer' 2 0), (New-Cell 'backhoe-loader' 3 0), (New-Cell 'wheel-loader' 4 0),
    (New-Cell 'skid-steer-loader' 0 1), (New-Cell 'motor-grader' 1 1), (New-Cell 'dump-truck' 2 1), (New-Cell 'articulated-hauler' 3 1), (New-Cell 'trencher' 4 1),
    (New-Cell 'compactor-road-roller' 0 2), (New-Cell 'plate-compactor-honda-engine' 0 2)
)

Add-SectionCells $cells 5 617 503 315 28 5 3 @(
    (New-Cell 'portable-concrete-mixer-350l' 0 0), (New-Cell 'concrete-mixer-truck' 1 0), (New-Cell 'concrete-pump' 2 0), (New-Cell 'concrete-batch-plant' 3 0), (New-Cell 'concrete-cutting-machine' 4 0),
    (New-Cell 'concrete-buggy' 0 1), (New-Cell 'concrete-hopper' 1 1), (New-Cell 'concrete-sealer-applicator' 2 1), (New-Cell 'bump-cutter' 4 1),
    (New-Cell 'concrete-edger' 0 2), (New-Cell 'concrete-groover' 1 2)
)

Add-SectionCells $cells 514 625 505 262 28 5 2 @(
    (New-Cell 'tower-crane' 0 0), (New-Cell 'mobile-crane' 1 0), (New-Cell 'chain-block-chain-hoist' 2 0), (New-Cell 'forklift' 3 0), (New-Cell 'manlift-boom-lift' 4 0),
    (New-Cell 'scissor-lift' 0 1), (New-Cell 'hand-truck-dolly' 1 1), (New-Cell 'pallet-jack' 2 1)
)

Add-SectionCells $cells 5 940 503 162 28 5 1 @(
    (New-Cell 'drill-rig' 0 0), (New-Cell 'pile-boring-machine' 1 0), (New-Cell 'pile-driver' 2 0), (New-Cell 'rock-breaker' 3 0), (New-Cell 'foundation-auger' 4 0)
)

Add-SectionCells $cells 514 896 505 213 28 5 2 @(
    (New-Cell 'laser-level' 0 0), (New-Cell 'total-station' 1 0), (New-Cell 'theodolite' 2 0), (New-Cell 'digital-measuring-tape' 3 0), (New-Cell 'gps-survey-equipment' 4 0),
    (New-Cell 'construction-calculator' 0 1)
)

Add-SectionCells $cells 5 1108 503 220 28 5 2 @(
    (New-Cell 'site-generator-5kva' 0 0), (New-Cell 'large-generator' 1 0), (New-Cell 'light-tower' 2 0), (New-Cell 'portable-floodlight' 3 0), (New-Cell 'ups-system' 4 0),
    (New-Cell 'digital-multimeter' 0 1), (New-Cell 'voltage-tester' 1 1), (New-Cell 'extension-reels-power-strips' 2 1)
)

Add-SectionCells $cells 514 1116 505 216 28 5 2 @(
    (New-Cell 'safety-helmet-hard-hat' 0 0), (New-Cell 'safety-boots-steel-toe' 1 0), (New-Cell 'reflective-safety-vest' 2 0), (New-Cell 'safety-gloves' 3 0), (New-Cell 'safety-goggles-face-shield' 4 0),
    (New-Cell 'ear-muffs-earplugs' 0 1), (New-Cell 'dust-mask-respirator' 1 1), (New-Cell 'safety-harness' 2 1), (New-Cell 'first-aid-kit' 3 1), (New-Cell 'fire-extinguisher' 4 1),
    (New-Cell 'site-safety-gear-kit' 0 0)
)

Add-SectionCells $cells 5 1337 403 195 28 3 1 @(
    (New-Cell 'submersible-pump' 0 0), (New-Cell 'wellpoint-system' 1 0), (New-Cell 'centrifugal-pump' 2 0)
)

Add-SectionCells $cells 411 1337 608 195 28 5 2 @(
    (New-Cell 'scaffolding-system' 0 0), (New-Cell 'mobile-scaffolding-set' 0 0), (New-Cell 'safety-barriers-barricades' 1 0), (New-Cell 'nails-bolts-screws-assorted' 2 0), (New-Cell 'cement-bags' 3 0), (New-Cell 'welding-machine-electrodes' 4 0),
    (New-Cell 'paint-sprayer' 0 1), (New-Cell 'caulk-sealant' 1 1), (New-Cell 'sandpaper-abrasives' 2 1), (New-Cell 'toolbox-tool-storage-cabinet' 3 1 2)
)

$sheet = [System.Drawing.Bitmap]::FromFile($Source)

try {
    $results = foreach ($cell in $cells) {
        Export-ToolImage $sheet $cell
    }
} finally {
    $sheet.Dispose()
}

$results | Group-Object { ($_ -split ' ')[0] } | ForEach-Object {
    "$($_.Name): $($_.Count)"
}
