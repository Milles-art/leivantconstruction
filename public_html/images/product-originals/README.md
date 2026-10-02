# High Quality Product Images

Place final product photos in this folder using the product slug as the filename.

Examples:

- `claw-hammer.png`
- `electric-drill.jpg`
- `portable-concrete-mixer-350l.png`
- `site-generator-5kva.webp`

Then run:

```powershell
powershell -ExecutionPolicy Bypass -File scripts\import-high-quality-product-images.ps1
.\.runtime\php\php.exe artisan migrate --seed --force
```

The import script converts images to optimized WebP files in `public/images/catalog-tools`, then the Laravel seeder copies them into public product storage.

