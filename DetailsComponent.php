<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Gloudemans\Shoppingcart\Facades\Cart;

class DetailsComponent extends Component
{
    public $slug;
    public function mount($slug){
        $this->slug = $slug;
    }

        public function store($product_id, $product_name, $product_price){
            Cart::instance('cart')->add($product_id, $product_name, 1, $product_price)->associate('App\Models\Product');
            return redirect()->route('cart');
        }

        public function addToWishlist($product_id, $product_name, $product_price){
            Cart::instance('wishlist')->add($product_id, $product_name, 1, $product_price)->associate('App\Models\Product');
            flash()->success('Product added to wishlist');
        }


        public function removeFromWishlist($product_id){
            foreach(Cart::instance('wishlist')->content() as $witem){
                if($witem->id == $product_id){
                    Cart::instance('wishlist')->remove($witem->rowId);
                    flash()->success('Product removed from wishlist');

                }
            }
        }

    public function render()
    {

        $product = Product::where('slug', $this->slug)->first();
        //$size=$product->size;
        $categories = Category::all();
        $image=$product->image;
        $images=json_decode($product->images);
        array_splice($images, 0, 0, $image);
        $related_products = Product::where('category_id', $product->category_id)->get();
        $new_products=Product::latest()->take(3)->get();
        return view('livewire.details-component',[
           'product' => $product
           ,'categories' => $categories,
           'related_products' => $related_products,
           'new_products' => $new_products,
           'images' => $images
       ])->layout('layouts.app');
    }
}
