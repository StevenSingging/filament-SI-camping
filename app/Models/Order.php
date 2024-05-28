<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'grand_total',
        'payment_method',
        'payment_status',
        'date_of_return',
        'notes',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function items(){
        return $this->hasMany(Orderitem::class);
    }

    public function personal_data(){
        return $this->hasOne(PersonalData::class);
    }
}
