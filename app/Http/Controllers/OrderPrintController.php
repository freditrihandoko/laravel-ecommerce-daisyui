<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ShippingInformation;
use Illuminate\Http\Request;

class OrderPrintController extends Controller
{
    public function show(Order $order)
    {
        $order->load(['items.product', 'items.variant', 'shippingMethod', 'ShippingInformation', 'user', 'status']);
        return view('orders-print', compact('order'));
    }
}
