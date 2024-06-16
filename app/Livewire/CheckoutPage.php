<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Models\Order;
use App\Models\PersonalData;
use Stripe\Checkout\Session;
use Livewire\Component;
use Stripe\Stripe;
use Livewire\WithFileUploads;

class CheckoutPage extends Component
{
    use WithFileUploads;
    public $first_name;
    public $last_name;
    public $phone;
    public $id_card;
    public $payment_method;
    public $date_of_return;

    public function mount(){
        $cart_items = CartManagement::getCartItemsFromCookie();
        if(count($cart_items) == 0){
            return redirect('/products');
        }
    }

    public function placeOrder(){

        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'id_card' => 'required|image|max:1024',
            'date_of_return' => 'required',
            'payment_method' => 'required'
        ]);

        $cart_items = CartManagement::getCartItemsFromCookie();
        $line_items = [];

        foreach($cart_items as $item){
            $line_items[] = [
                'price_data' => [
                    'currency' => 'idr',
                    'unit_amount' => $item['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $item['name'],
                    ]
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);
        $order->payment_method = $this->payment_method;
        $order->status = 'new';
        $order->date_of_return = $this->date_of_return;
        $order->notes = 'Order placed by ' . auth()->user()->name;

        $personal_data = New PersonalData();
        $personal_data->first_name = $this->first_name;
        $personal_data->last_name = $this->last_name;
        $personal_data->phone = $this->phone;
        $personal_data->id_card = $this->id_card->store('personal_datas','public');

        $redirect_url = '';

        if($this->payment_method == 'credit'){
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $sessionCheckout = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => auth()->user()->email,
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cancel')
            ]);

            $redirect_url = $sessionCheckout->url;
        }else{
            $redirect_url = route('success');
        }

        $order->save();
        $personal_data->order_id = $order->id;
        $personal_data->save();
        $order->items()->createMany($cart_items);
        CartManagement::ClearCartItems();
        return redirect($redirect_url);
    }

    public function render()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);
        return view('livewire.checkout-page',[
            'cart_items' => $cart_items,
            'grand_total' => $grand_total,
        ]);
    }
}
