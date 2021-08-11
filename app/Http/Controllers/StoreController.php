<?php

namespace App\Http\Controllers;

use App\Models\Product\Item;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index()
    {
        $products = Product::with('label:id,image')
                    ->where(['is_featured' => 1, 'is_published' => 1])
                    ->orderBy('created_at', 'DESC')
                    ->paginate(16);
        return view('store.index', compact('products'));
    }

    public function showByCategory(ProductCategory $category)
    {
        $products = Product::with('label:id,image')
                    ->where(['product_category_id' => $category->id, 'is_published' => 1])
                    ->orderBy('created_at', 'DESC')
                    ->paginate(16);
        return view('store.category', compact('products'));
    }

    public function showProduct(Product $product)
    {
        $items = Item::where(['product_id' => $product->id, 'is_published' => 1])
                ->orderBy('created_at')
                ->get();
        return view('store.view-detail', compact('product', 'items'));
    }

    public function purchaseItem(Request $request)
    {
        $request->validate([
            'item' => 'required'
        ]);

        $item = Item::findOrFail($request->item);
        $userBalance = Auth::user()->balance;

        if ( $item->stock < 1 ) return redirect()->back()->with("error", "Unable to complete the purchased item. Item out of stock!");

        if ( !($userBalance < $item->price) ) {
            DB::beginTransaction();
            try {
                Auth::user()->update([
                    'balance' => $userBalance - $item->price
                ]);
                $item->update([
                    'stock' => $item->stock - 1
                ]);
                DB::commit();
                return redirect()->back()->with("success", "Successfully purchased item.");
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with("error", "Something went wrong.");
            }
        } else {
            return redirect()->back()->with("error", "Unable to complete the purchased item. Not enough balance.");
        }
    }
}
