<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Title('Success')]
class SuccessPage extends Component
{
    public function render()
    {
        $latest_order = Order::with('personal_data')->where('user_id',auth()->user()->id)->latest()->first();
        return view('livewire.success-page',[
            'order' => $latest_order,
        ]);
    }
}
