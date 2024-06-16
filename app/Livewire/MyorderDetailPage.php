<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PersonalData;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('My Order')]
class MyorderDetailPage extends Component
{
    public $order_id;

    public function mount($order_id){
        $this->order_id = $order_id;
    }

    public function render()
    {
        $order_items = OrderItem::with('product')->where('order_id', $this->order_id)->get();
        $personal_data = PersonalData::where('order_id',$this->order_id)->first();
        $order = Order::where('id' , $this->order_id)->first();
        return view('livewire.myorder-detail-page',[
            'order_items' => $order_items,
            'personal_data' => $personal_data,
            'order' => $order,
        ]);
    }
}
