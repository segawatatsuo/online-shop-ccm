<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;


class ProductImageController extends Controller
{
    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);

        // ストレージから削除
        if (Storage::disk('public')->exists($image->filename)) {
            Storage::disk('public')->delete($image->filename);
        }

        $productId = $image->product_id;
        $image->delete();

        return redirect()->route('admin.products.edit', $productId)
                         ->with('success', '画像を削除しました。');
    }
}

