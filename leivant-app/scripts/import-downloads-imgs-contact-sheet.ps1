param(
    [string] $Source = "C:\Users\Esrom Mussa\Downloads\imgs\white-background-tools-contact-sheet.jpg",
    [switch] $Replace
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$outDir = Join-Path $root 'public\images\catalog-tools'
$sourcesPath = Join-Path $outDir 'sources.json'
$cwebp = Join-Path $root '.runtime\tools\libwebp-1.6.0-windows-x64\bin\cwebp.exe'
$tempDir = Join-Path $root 'storage\app\temp-contact-sheet'

if (!(Test-Path -LiteralPath $Source)) {
    throw "Contact sheet was not found at $Source"
}

if (!(Test-Path -LiteralPath $cwebp)) {
    throw "cwebp.exe was not found at $cwebp"
}

New-Item -ItemType Directory -Force -Path $outDir | Out-Null
New-Item -ItemType Directory -Force -Path $tempDir | Out-Null

Add-Type -AssemblyName System.Drawing

function New-ContactItem {
    param([int] $Number, [string] $Slug, [string] $Title)

    [pscustomobject]@{
        Number = $Number
        Slug = $Slug
        Title = $Title
    }
}

$items = @(
    (New-ContactItem 1 'claw-hammer' 'Claw Hammer'),
    (New-ContactItem 2 'sledgehammer' 'Sledgehammer'),
    (New-ContactItem 3 'chisel-set' 'Chisel Set'),
    (New-ContactItem 4 'screwdriver-set' 'Screwdriver Set'),
    (New-ContactItem 5 'tape-measure-30ft' 'Tape Measure 30ft'),
    (New-ContactItem 6 'spirit-level' 'Spirit Level'),
    (New-ContactItem 7 'pliers-set' 'Pliers Set'),
    (New-ContactItem 8 'wrench-set' 'Wrench Set'),
    (New-ContactItem 9 'hand-saw' 'Hand Saw'),
    (New-ContactItem 10 'hacksaw' 'Hacksaw'),
    (New-ContactItem 11 'masonry-trowel' 'Masonry Trowel'),
    (New-ContactItem 12 'plastering-trowel' 'Plastering Trowel'),
    (New-ContactItem 13 'hawk-board' 'Hawk Board'),
    (New-ContactItem 14 'concrete-float' 'Concrete Float'),
    (New-ContactItem 15 'bull-float' 'Bull Float'),
    (New-ContactItem 16 'digging-bar' 'Digging Bar'),
    (New-ContactItem 17 'pickaxe' 'Pickaxe'),
    (New-ContactItem 18 'shovel-spade' 'Shovel / Spade'),
    (New-ContactItem 19 'heavy-duty-wheelbarrow' 'Heavy Duty Wheelbarrow'),
    (New-ContactItem 20 'caulk-gun' 'Caulk Gun'),
    (New-ContactItem 21 'electric-drill' 'Electric Drill'),
    (New-ContactItem 22 'impact-driver' 'Impact Driver'),
    (New-ContactItem 23 'rotary-hammer-drill' 'Rotary Hammer Drill'),
    (New-ContactItem 24 'circular-saw' 'Circular Saw'),
    (New-ContactItem 25 'professional-angle-grinder' 'Professional Angle Grinder'),
    (New-ContactItem 26 'reciprocating-saw' 'Reciprocating Saw'),
    (New-ContactItem 27 'jigsaw' 'Jigsaw'),
    (New-ContactItem 28 'belt-sander' 'Belt Sander'),
    (New-ContactItem 29 'concrete-vibrator-38mm' 'Concrete Vibrator 38mm'),
    (New-ContactItem 30 'power-trowel' 'Power Trowel'),
    (New-ContactItem 31 'bench-grinder' 'Bench Grinder'),
    (New-ContactItem 32 'nail-gun' 'Nail Gun'),
    (New-ContactItem 33 'crawler-excavator' 'Crawler Excavator'),
    (New-ContactItem 34 'wheeled-excavator' 'Wheeled Excavator'),
    (New-ContactItem 35 'bulldozer' 'Bulldozer'),
    (New-ContactItem 36 'backhoe-loader' 'Backhoe Loader'),
    (New-ContactItem 37 'wheel-loader' 'Wheel Loader'),
    (New-ContactItem 38 'skid-steer-loader' 'Skid Steer Loader'),
    (New-ContactItem 39 'motor-grader' 'Motor Grader'),
    (New-ContactItem 40 'dump-truck' 'Dump Truck'),
    (New-ContactItem 41 'articulated-hauler' 'Articulated Hauler'),
    (New-ContactItem 42 'trencher' 'Trencher'),
    (New-ContactItem 43 'compactor-road-roller' 'Compactor / Road Roller'),
    (New-ContactItem 44 'portable-concrete-mixer-350l' 'Portable Concrete Mixer 350L'),
    (New-ContactItem 45 'concrete-mixer-truck' 'Concrete Mixer Truck'),
    (New-ContactItem 46 'concrete-pump' 'Concrete Pump'),
    (New-ContactItem 47 'concrete-batch-plant' 'Concrete Batch Plant'),
    (New-ContactItem 48 'concrete-cutting-machine' 'Concrete Cutting Machine'),
    (New-ContactItem 49 'concrete-buggy' 'Concrete Buggy'),
    (New-ContactItem 50 'concrete-hopper' 'Concrete Hopper'),
    (New-ContactItem 51 'concrete-sealer-applicator' 'Concrete Sealer & Applicator'),
    (New-ContactItem 52 'bump-cutter' 'Bump Cutter'),
    (New-ContactItem 53 'concrete-edger' 'Concrete Edger'),
    (New-ContactItem 54 'concrete-groover' 'Concrete Groover'),
    (New-ContactItem 55 'tower-crane' 'Tower Crane'),
    (New-ContactItem 56 'mobile-crane' 'Mobile Crane'),
    (New-ContactItem 57 'chain-block-chain-hoist' 'Chain Block / Chain Hoist'),
    (New-ContactItem 58 'forklift' 'Forklift'),
    (New-ContactItem 59 'manlift-boom-lift' 'Manlift / Boom Lift'),
    (New-ContactItem 60 'scissor-lift' 'Scissor Lift'),
    (New-ContactItem 61 'hand-truck-dolly' 'Hand Truck / Dolly'),
    (New-ContactItem 62 'pallet-jack' 'Pallet Jack'),
    (New-ContactItem 63 'drill-rig' 'Drill Rig'),
    (New-ContactItem 64 'pile-boring-machine' 'Pile Boring Machine'),
    (New-ContactItem 65 'pile-driver' 'Pile Driver'),
    (New-ContactItem 66 'rock-breaker' 'Rock Breaker'),
    (New-ContactItem 67 'foundation-auger' 'Foundation Auger'),
    (New-ContactItem 68 'laser-level' 'Laser Level'),
    (New-ContactItem 69 'total-station' 'Total Station'),
    (New-ContactItem 70 'theodolite' 'Theodolite'),
    (New-ContactItem 71 'digital-measuring-tape' 'Digital Measuring Tape'),
    (New-ContactItem 72 'gps-survey-equipment' 'GPS Survey Equipment'),
    (New-ContactItem 73 'construction-calculator' 'Construction Calculator'),
    (New-ContactItem 74 'site-generator-5kva' 'Portable Generator Small'),
    (New-ContactItem 75 'large-generator' 'Large Generator'),
    (New-ContactItem 76 'light-tower' 'Light Tower'),
    (New-ContactItem 77 'portable-floodlight' 'Portable Floodlight'),
    (New-ContactItem 78 'ups-system' 'UPS System'),
    (New-ContactItem 79 'digital-multimeter' 'Digital Multimeter'),
    (New-ContactItem 80 'voltage-tester' 'Voltage Tester'),
    (New-ContactItem 81 'extension-reels-power-strips' 'Extension Reels / Power Strips'),
    (New-ContactItem 82 'safety-helmet-hard-hat' 'Safety Helmet / Hard Hat'),
    (New-ContactItem 83 'safety-boots-steel-toe' 'Safety Boots Steel Toe'),
    (New-ContactItem 84 'reflective-safety-vest' 'Reflective Safety Vest'),
    (New-ContactItem 85 'safety-gloves' 'Safety Gloves'),
    (New-ContactItem 86 'safety-goggles-face-shield' 'Safety Goggles / Face Shield'),
    (New-ContactItem 87 'face-shield' 'Face Shield'),
    (New-ContactItem 88 'ear-muffs-earplugs' 'Ear Muffs / Earplugs'),
    (New-ContactItem 89 'earplugs' 'Earplugs'),
    (New-ContactItem 90 'dust-mask-respirator' 'Dust Mask / Respirator'),
    (New-ContactItem 91 'respirator' 'Respirator'),
    (New-ContactItem 92 'safety-harness' 'Safety Harness'),
    (New-ContactItem 93 'first-aid-kit' 'First Aid Kit'),
    (New-ContactItem 94 'fire-extinguisher' 'Fire Extinguisher'),
    (New-ContactItem 95 'submersible-pump' 'Submersible Pump'),
    (New-ContactItem 96 'wellpoint-system' 'Wellpoint System'),
    (New-ContactItem 97 'centrifugal-pump' 'Centrifugal Pump'),
    (New-ContactItem 98 'scaffolding-system' 'Scaffolding System'),
    (New-ContactItem 99 'safety-barriers-barricades' 'Safety Barriers / Barricades'),
    (New-ContactItem 100 'nails-bolts-screws-assorted' 'Nails, Bolts, Screws Assorted'),
    (New-ContactItem 101 'cement-bags' 'Cement Bags'),
    (New-ContactItem 102 'welding-machine-electrodes' 'Welding Machine + Electrodes'),
    (New-ContactItem 103 'paint-sprayer' 'Paint Sprayer'),
    (New-ContactItem 104 'caulk-sealant' 'Caulk / Sealant'),
    (New-ContactItem 105 'sandpaper-abrasives' 'Sandpaper / Abrasives'),
    (New-ContactItem 106 'toolbox-tool-storage-cabinet' 'Toolbox / Tool Storage Cabinet')
)

function Get-ContentBounds {
    param([System.Drawing.Bitmap] $Image)

    $minX = $Image.Width
    $minY = $Image.Height
    $maxX = -1
    $maxY = -1

    for ($y = 0; $y -lt $Image.Height; $y++) {
        for ($x = 0; $x -lt $Image.Width; $x++) {
            $pixel = $Image.GetPixel($x, $y)

            if ($pixel.R -lt 244 -or $pixel.G -lt 244 -or $pixel.B -lt 244) {
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

    $pad = 8
    $x = [Math]::Max(0, $minX - $pad)
    $y = [Math]::Max(0, $minY - $pad)
    $right = [Math]::Min($Image.Width - 1, $maxX + $pad)
    $bottom = [Math]::Min($Image.Height - 1, $maxY + $pad)

    return New-Object System.Drawing.Rectangle $x, $y, ($right - $x + 1), ($bottom - $y + 1)
}

function Export-ContactItem {
    param([System.Drawing.Bitmap] $Sheet, [object] $Item)

    $target = Join-Path $outDir "$($Item.Slug).webp"

    if ((Test-Path -LiteralPath $target) -and -not $Replace) {
        return "skip $($Item.Slug)"
    }

    $cols = 6
    $rows = 18
    $cellW = [Math]::Floor($Sheet.Width / $cols)
    $cellH = [Math]::Floor($Sheet.Height / $rows)
    $index = $Item.Number - 1
    $col = $index % $cols
    $row = [Math]::Floor($index / $cols)

    $cropX = [int]($col * $cellW)
    $cropY = [int]($row * $cellH)
    $cropW = [int]$cellW
    $cropH = [int]([Math]::Min($cellH, $Sheet.Height - $cropY))
    $labelCut = [int]($cropH * 0.18)
    $sourceRect = New-Object System.Drawing.Rectangle $cropX, $cropY, $cropW, ($cropH - $labelCut)
    $rawCrop = $Sheet.Clone($sourceRect, $Sheet.PixelFormat)
    $bounds = Get-ContentBounds $rawCrop
    $crop = $rawCrop.Clone($bounds, $rawCrop.PixelFormat)

    $canvas = New-Object System.Drawing.Bitmap 900, 650
    $graphics = [System.Drawing.Graphics]::FromImage($canvas)
    $graphics.Clear([System.Drawing.Color]::White)
    $graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
    $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $graphics.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality

    $maxW = 820
    $maxH = 540
    $scale = [Math]::Min($maxW / $crop.Width, $maxH / $crop.Height)
    $drawW = [int][Math]::Round($crop.Width * $scale)
    $drawH = [int][Math]::Round($crop.Height * $scale)
    $drawX = [int][Math]::Round((900 - $drawW) / 2)
    $drawY = [int][Math]::Round((650 - $drawH) / 2)

    $graphics.DrawImage($crop, $drawX, $drawY, $drawW, $drawH)

    $tempPng = Join-Path $tempDir "$($Item.Slug).png"
    $canvas.Save($tempPng, [System.Drawing.Imaging.ImageFormat]::Png)
    & $cwebp -quiet -q 92 -m 6 -sharp_yuv $tempPng -o $target

    if ($LASTEXITCODE -ne 0 -or !(Test-Path -LiteralPath $target)) {
        throw "Failed to convert $($Item.Slug) to WebP"
    }

    $graphics.Dispose()
    $canvas.Dispose()
    $crop.Dispose()
    $rawCrop.Dispose()

    return "import $($Item.Slug)"
}

$sheet = [System.Drawing.Bitmap]::FromFile($Source)

try {
    $results = foreach ($item in $items) {
        Export-ContactItem $sheet $item
    }
} finally {
    $sheet.Dispose()
}

function ConvertTo-Hashtable {
    param($InputObject)

    if ($null -eq $InputObject) {
        return @{}
    }

    if ($InputObject -is [System.Collections.IDictionary]) {
        $hash = @{}
        foreach ($key in $InputObject.Keys) {
            $hash[$key] = ConvertTo-Hashtable $InputObject[$key]
        }
        return $hash
    }

    if ($InputObject -is [pscustomobject]) {
        $hash = @{}
        foreach ($property in $InputObject.PSObject.Properties) {
            $hash[$property.Name] = ConvertTo-Hashtable $property.Value
        }
        return $hash
    }

    return $InputObject
}

$sources = if (Test-Path -LiteralPath $sourcesPath) {
    ConvertTo-Hashtable (Get-Content -LiteralPath $sourcesPath -Raw | ConvertFrom-Json)
} else {
    @{}
}

foreach ($item in $items) {
    $sources[$item.Slug] = @{
        source = 'Downloads imgs contact sheet'
        license = 'User-provided product image sheet'
        title = $item.Title
        original_file = (Split-Path -Leaf $Source)
    }
}

$sources.GetEnumerator() |
    Sort-Object Name |
    ForEach-Object { [pscustomobject]@{ Key = $_.Key; Value = $_.Value } } |
    ForEach-Object -Begin { $ordered = [ordered]@{} } -Process { $ordered[$_.Key] = $_.Value } -End {
        $ordered | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath $sourcesPath -Encoding UTF8
    }

$results | Group-Object { ($_ -split ' ')[0] } | ForEach-Object {
    "$($_.Name): $($_.Count)"
}
