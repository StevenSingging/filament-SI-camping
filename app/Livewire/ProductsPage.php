<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Product')]
class ProductsPage extends Component
{
    use WithPagination;

    use LivewireAlert;
    
    #[Url]
    public $selected_categories = [];

    #[Url]
    public $on_sale;

    #[Url]
    public $in_stock;

    #[Url]
    public $sort = 'latest';

    //add product to cart method
    public function addToCart($product_id){
        $total_count = CartManagement::addItemToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);

        $this->alert('success', 'Product added to the cart successfully', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
           ]);
    }

    public function render()
    {
        $productQuery = Product::query()->where('is_active',1);

        if(!empty($this->selected_categories)){
            $productQuery->whereIn('category_id',$this->selected_categories);
        }

        if($this->on_sale){
            $productQuery->where('on_sale',1);
        }

        if($this->in_stock){
            $productQuery->where('in_stock',1);
        }

        if($this->sort == 'latest'){
            $productQuery->latest();
        }

        if($this->sort == 'price'){
            $productQuery->orderBy('price');
        }


        return view('livewire.products-page',[
            'products' => $productQuery->paginate(9),
            'categories' => Category::where('is_active',1)->get(['id','name','slug'])
        ]);
    }
}
