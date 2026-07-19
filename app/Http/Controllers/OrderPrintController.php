<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderPrintController extends Controller
{
    public function show(Request $request, Order $order)
    {
        $user = $request->user();
        if (! $user || ! $user->isEmployee()) {
            abort(403);
        }

        $type = $request->query('type', 'invoice');
        $order->load(['items.product', 'user']);

        return view('orders.print', [
            'order' => $order,
            'type' => $type,
        ]);
    }
}
