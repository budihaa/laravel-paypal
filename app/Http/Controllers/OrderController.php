<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('order/index');
    }

    public function capturePayment(Request $request)
    {
        $order = Order::create([
            'paypal_id' => $request->paypal_id,
            'paypal_order_id' => $request->paypal_order_id,
            'status' => $request->status,
            'customer_email' => $request->customer_email,
            'customer_name' => $request->customer_name,
            'amount' => $request->amount,
            'currency_code' => $request->currency_code,
        ]);
        return $order;
    }
}
