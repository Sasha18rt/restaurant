<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Dish;
use App\Models\DishType;
use App\Models\AddOn;

class StripeController extends Controller
{
    public function stripe(Request $request)
    {
        $stripe = new StripeClient(config('stripe.stripe_sk'));

        $selectedOrderIds = $request->input('orders', []);

        $orders = Order::whereIn('id', $selectedOrderIds)->get();

        $lineItems = [];

        foreach ($orders as $order) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $order->dish->title,
                    ],
                    'unit_amount' => $order->price * 100, 
                ],
                'quantity' => $order->quantity,
            ];
        }

        if (empty($lineItems)) {
            return redirect()->route('stripe.cancel')->with('error', 'No items selected for payment.');
        }

        $response = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
        ]);

        if (isset($response->id) && $response->id != '') {
            session()->put('orders', $selectedOrderIds);
            return response()->json(['url' => $response->url]);
        } else {
            return response()->json(['error' => 'Failed to create payment session.'], 500);
        }
    }

    public function success(Request $request)
{
    if ($request->has('session_id')) {
        $stripe = new StripeClient(config('stripe.stripe_sk'));
        $response = $stripe->checkout->sessions->retrieve($request->session_id);

        $orderIds = session()->get('orders', []);

        if (empty($orderIds)) {
            return redirect()->route('cancel')->with('error', 'No orders found.');
        }

        $firstOrder = Order::find($orderIds[0]);
        $tableId = $firstOrder->table_id;

        $payment = new Payment();
        $payment->amount = collect($orderIds)->sum(function($orderId) {
            $order = Order::find($orderId);
            return $order ? $order->price : 0;
        });
        $payment->currency = $response->currency;
        $payment->customer_name = $response->customer_details->name;
        $payment->customer_email = $response->customer_details->email;
        $payment->status = $response->status;
        $payment->payment_method = "Stripe";
        $payment->save();

        foreach ($orderIds as $orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->payment_id = $payment->id;
                $order->payment_status = 'paid'; 
                $order->save();
            }
        }

        session()->forget('orders');

        return redirect()->route('table.show', ['id' => $tableId])->with('success', 'Payment is successful');
    } else {
        return redirect()->route('cancel');
    }
}


public function payAll(Request $request, $tableId)
{
    $stripe = new StripeClient(config('stripe.stripe_sk'));

    $orders = Order::where('table_id', $tableId)
        ->where('payment_status', 'unpaid')
        ->get();

    $lineItems = [];

    foreach ($orders as $order) {
        $totalPrice = $order->price;

        foreach ($order->addons as $addon) {
            $totalPrice += $addon->price;
        }

        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $order->dish->title,
                ],
                'unit_amount' => $totalPrice * 100, 
            ],
            'quantity' => $order->quantity,
        ];
    }

    if (empty($lineItems)) {
        return response()->json(['error' => 'No items to pay for.'], 400);
    }

    try {
        $response = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}&table_id=' . $tableId,
            'cancel_url' => route('stripe.cancel', ['table_id' => $tableId]),
        ]);

        if (isset($response->id) && $response->id != '') {
            session()->put('orders', $orders->pluck('id')->toArray());
            return response()->json(['url' => $response->url]);
        } else {
            return response()->json(['error' => 'Failed to create payment session.'], 500);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create payment session.'], 500);
    }
}

   

    public function paySingleItem(Request $request)
{
    $stripe = new StripeClient(config('stripe.stripe_sk'));

    $dish = Dish::findOrFail($request->input('dish_id'));
    $quantity = $request->input('quantity', 1);
    $addons = $request->input('addons', []);

    $totalPrice = $dish->price * $quantity;
    foreach ($addons as $addonId) {
        $addon = AddOn::find($addonId);
        if ($addon) {
            $totalPrice += $addon->price * $quantity;
        }
    }

    session()->put('dish_id', $dish->id);
    session()->put('table_id', $request->input('table_id'));
    session()->put('quantity', $quantity);
    session()->put('addons', $addons);
    session()->put('comment', $request->input('comment'));
    session()->put('total_price', $totalPrice);

    $response = $stripe->checkout->sessions->create([
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $dish->title,
                ],
                'unit_amount' => $totalPrice * 100, 
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => route('stripe.successSingleItem') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => route('stripe.cancel'),
    ]);

    if (isset($response->id) && $response->id != '') {
        return redirect($response->url);
    } else {
        return redirect()->route('table.show', ['id' => $request->input('table_id')])
            ->with('error', 'Failed to create payment session.');
    }
}


    public function successSingleItem(Request $request)
    {
        if ($request->has('session_id')) {
            $stripe = new StripeClient(config('stripe.stripe_sk'));
            $response = $stripe->checkout->sessions->retrieve($request->session_id);
    

            $dishId = session()->get('dish_id');
            $tableId = session()->get('table_id');
            $quantity = session()->get('quantity');
            $addons = session()->get('addons');
            $comment = session()->get('comment');
    
            if (!$dishId || !$quantity || !$tableId) {
                return redirect()->route('table.show', ['id' => $tableId])->with('error', 'Invalid session data.');
            }
    
     
            $order = new Order();
            $order->table_id = $tableId;
            $order->dish_id = $dishId;
            $order->quantity = $quantity;
            $order->price = session()->get('total_price'); 
            $order->status = 'active';
            $order->payment_status = 'paid';
            $order->comment = $comment;
    
            $order->save();
    

            if (!empty($addons)) {
                foreach ($addons as $addonId) {
                    $order->addons()->attach($addonId);
                }
            }
    
          
            session()->forget(['dish_id', 'table_id', 'quantity', 'addons', 'comment', 'total_price']);
    
            return redirect()->route('table.show', ['id' => $tableId])->with('success', 'Order created and payment successful.');
        } else {
            return redirect()->route('table.show', ['id' => $request->input('table_id')])->with('error', 'Payment failed.');
        }
    }
    
    public function cancel()
    {
        return redirect()->back()->with('error', 'Payment was cancelled.');
    }
}
