<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // $table->id();
    // //order_number is a unique string that identifies an order
    // $table->string('order_number')->unique();
    // //outlet_id is a foreign key that references the id column on the outlets table
    // $table->foreignId('outlet_id')->constrained('outlets');
    // //sub_total is a decimal that represents the total amount of the order before tax and discount
    // $table->decimal('sub_total', 10, 2);
    // //total price is a decimal that represents the total amount of the order after tax and discount
    // $table->decimal('total_price', 10, 2);
    // //total items is an integer that represents the total number of items in the order
    // $table->integer('total_items');
    // //tax is a decimal that represents the tax amount for the order
    // $table->decimal('tax', 10, 2);
    // //discount is a decimal that represents the discount amount for the order
    // $table->decimal('discount', 10, 2);

    // //payment method is a string that represents the payment method used to pay for the order
    // $table->string('payment_method');
    // //status is a string that represents the status of the order
    // $table->string('status');
    // //cashier_id is a foreign key that references the id column on the users table
    // $table->foreignId('cashier_id')->constrained('users');

    protected $fillable = [
        'order_number',
        'outlet_id',
        'sub_total',
        'total_price',
        'total_items',
        'tax',
        'discount',
        'payment_method',
        'status',
        'cashier_id'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

}
