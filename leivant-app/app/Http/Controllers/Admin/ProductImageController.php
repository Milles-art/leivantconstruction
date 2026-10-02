<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function update(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:180'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $image->update($validated);
        ActivityLog::record('product_image.updated', 'Updated image for '.$product->name, $product);

        return back()->with('success', 'Image updated.');
    }

    public function primary(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true, 'sort_order' => 0]);
        ActivityLog::record('product_image.primary', 'Set primary image for '.$product->name, $product);

        return back()->with('success', 'Primary image updated.');
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();
        ActivityLog::record('product_image.deleted', 'Deleted image for '.$product->name, $product);

        return back()->with('success', 'Image deleted.');
    }
}
