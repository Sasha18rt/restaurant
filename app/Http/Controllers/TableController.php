<?php

namespace App\Http\Controllers;

use App\Models\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\Dish;
use App\Models\Order;
use App\Models\Payment;
use App\Models\DishType;
use App\Models\AddOn;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('admin.admin_table', compact('tables'));
    }

    public function show($id)
    {
        $table = Table::findOrFail($id);
        $orders = $table->orders()->where('status', 'active')->get();
        $dishes = Dish::all();
        return view('admin.table_show', compact('table', 'orders', 'dishes'));
    }

    public function generateQRCode($id)
    {
        $url = route('table.show', ['id' => $id]);
        $qrCode = QrCode::format('svg')->size(300)->generate($url);

        $fileName = 'qrcodes/table_' . $id . '.svg';
        Storage::disk('public')->put($fileName, $qrCode);

        return response($qrCode, 200)->header('Content-Type', 'image/svg+xml');
    }

    public function downloadQRCode($id)
    {
        $fileName = 'qrcodes/table_' . $id . '.svg';

        if (Storage::disk('public')->exists($fileName)) {
            return Storage::disk('public')->download($fileName);
        }

        return redirect()->back()->with('error', 'QR Code not found.');
    }

    public function order(Request $request, $id)
{
    $table = Table::findOrFail($id);
    $dish = Dish::findOrFail($request->input('dish_id'));

    $order = new Order();
    $order->table_id = $table->id;
    $order->dish_id = $dish->id;
    $order->quantity = $request->input('quantity');
    
    $totalPrice = $dish->price;

    if ($request->has('addons')) {
        foreach ($request->input('addons') as $addonId) {
            $addon = AddOn::findOrFail($addonId);
            $totalPrice += $addon->price;
        }
    }

    $order->price = $totalPrice;
    $order->status = 'active';
    $order->payment_status = 'unpaid';
    $order->save();

    // Attach add-ons to the order
    if ($request->has('addons')) {
        $order->addons()->attach($request->input('addons'));
    }

    return redirect()->route('table.show', ['id' => $table->id]);
}


public function pay(Request $request, $id)
{
    $table = Table::findOrFail($id);
    $orders = $table->orders()->whereIn('status', ['active', 'completed'])->get();
    $totalAmount = $orders->sum(function ($order) {
        return $order->price + $order->addons->sum('price');
    });

    // Create a Stripe session
    $stripe = new \Stripe\StripeClient(config('stripe.stripe_sk'));
    $response = $stripe->checkout->sessions->create([
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => "Payment for Table " . $table->id,
                ],
                'unit_amount' => $totalAmount * 100, 
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => route('stripe.success', ['id' => $table->id]),
        'cancel_url' => route('stripe.cancel'),
    ]);

    return redirect($response->url);
}

   

    public function selectTable()
    {
        $tables = Table::all();
        return view('order.select_table', compact('tables'));
    }

    public function create(Request $request)
    {
        $tableId = $request->query('table_id');

        if ($tableId === null || $tableId === '') {
            $tableId = 'takeaway';  
        }

        $orders = Order::where('table_id', $tableId == 'takeaway' ? null : $tableId)->where('status', 'in process')->get();

        return view('order.create', compact('tableId', 'orders'));
    }

    public function selectDishType(Request $request)
    {
        $tableId = $request->query('table_id');
        $dish_types = DishType::all();

        return view('order.select_dish_type', compact('tableId', 'dish_types'));
    }

    public function selectDish(Request $request)
    {
        $tableId = $request->query('table_id');
        $typeId = $request->query('type_id');
        $dishes = Dish::where('type_id', $typeId)->get();

        return view('order.select_dish', compact('tableId', 'typeId', 'dishes'));
    }

   public function addDetails(Request $request)
{
    $tableId = $request->query('table_id');
    $dishId = $request->query('dish_id');

    $dish = Dish::findOrFail($dishId);


    $add_ons = AddOn::join('dish_add_ons', 'add_ons.id', '=', 'dish_add_ons.addon_id')
        ->where('dish_add_ons.id', $dishId) 
        ->select('add_ons.id', 'add_ons.addon_name', 'add_ons.price') 
        ->get();

    return view('order.add_details', compact('tableId', 'dishId', 'dish', 'add_ons'));
}

public function showMenu($id)
{
    $table = Table::findOrFail($id);
    $dishTypes = DishType::all();
    $menu = Dish::all();

    return view('menu', compact('table', 'dishTypes', 'menu'));
}


public function addItem(Request $request)
{
    // Validate the input data
    $rules = [
        'dish_id' => 'required|exists:dishes,id',
        'quantity' => 'required|integer|min:1',
        'comment' => 'nullable|string',
    ];

    // If table_id is provided, validate it
    if ($request->input('table_id') !== null) {
        $rules['table_id'] = 'exists:tables,id';
    }

    $validatedData = $request->validate($rules);

    // Get the base price of the dish
    $dish = Dish::find($validatedData['dish_id']);
    $basePrice = $dish->price;

    // Calculate the total price including add-ons
    $totalPrice = $basePrice;
    if ($request->has('addons')) {
        foreach ($request->addons as $addon_id) {
            $addon = AddOn::find($addon_id);
            if ($addon) {
                $totalPrice += $addon->price;
            }
        }
    }

    // Create a new order and set the price to the calculated total for a single item
    $order = new Order();
    $order->table_id = $validatedData['table_id'] ?? null;
    $order->dish_id = $validatedData['dish_id'];
    $order->quantity = $validatedData['quantity'];
    $order->price = $totalPrice; // Store the price for one item including add-ons
    $order->status = 'in process';
    $order->comment = $validatedData['comment'] ?? null;
    $order->save();

    // Attach the add-ons to the order
    if ($request->has('addons')) {
        foreach ($request->addons as $addon_id) {
            $order->addons()->attach($addon_id);
        }
    }

    // Redirect back to the order creation page with a success message
    return redirect()->route('order.create', ['table_id' => $order->table_id])->with('success', 'Item added to order.');
}

    public function completeOrder(Request $request)
    {
        $tableId = $request->input('table_id');

        $query = Order::query()->where('status', 'in process');

        if ($tableId !== null) {
            $query->where('table_id', $tableId);
        } else {
            $query->whereNull('table_id');
        }

        $orders = $query->get();

        foreach ($orders as $order) {
            $order->update(['status' => 'active']);
        }

        return redirect()->route('order.create', ['table_id' => $tableId])->with('success', 'Order completed successfully!');
    }

    public function getAddons($dishId)
    {
        $addons = AddOn::join('dish_add_ons', 'add_ons.id', '=', 'dish_add_ons.addon_id')
            ->where('dish_add_ons.id', $dishId)
            ->select('add_ons.id', 'add_ons.addon_name')
            ->get();

        return response()->json($addons);
    }

    public function destroyLatest()
    {
        $latestTable = Table::orderBy('id', 'desc')->first();

        if ($latestTable) {
            $latestTable->delete();
            return redirect()->route('tables.index')->with('success', 'Table with the highest ID deleted successfully');
        } else {
            return redirect()->route('tables.index')->with('error', 'No table found to delete');
        }
    }

    public function orders()
{
    $orders = Order::whereIn('status', ['active', 'paid'])->get();
    return view('admin.orders', compact('orders'));
}

public function complete($id)
{
    $order = Order::findOrFail($id);

   
        $order->status = 'completed';

    $order->save();

    return redirect()->route('orders.index')->with('success', 'Order status updated successfully');
}







    public function removeItem($id)
{
    $order = Order::findOrFail($id);
    $order->delete();

    return redirect()->back()->with('success', 'Item removed from order.');
}

public function updateQuantity(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    $order = Order::findOrFail($id);
    $order->quantity = $request->input('quantity');
    $order->save();

    return redirect()->back()->with('success', 'Order quantity updated.');
}

public function editOrder($id)
{
    $order = Order::findOrFail($id);
    $dish = Dish::findOrFail($order->dish_id);
    $add_ons = AddOn::join('dish_add_ons', 'add_ons.id', '=', 'dish_add_ons.addon_id')
        ->where('dish_add_ons.id', $order->dish_id)
        ->select('add_ons.id', 'add_ons.addon_name', 'add_ons.price')
        ->get();

    $selected_add_ons = $order->addons->pluck('id')->toArray();

    return view('order.add_details', [
        'tableId' => $order->table_id,
        'dishId' => $order->dish_id,
        'dish' => $dish,
        'add_ons' => $add_ons,
        'quantity' => $order->quantity,
        'comment' => $order->comment,
        'orderId' => $order->id,
        'selected_add_ons' => $selected_add_ons,
    ]);
}

public function getLatestOrders()
{
    $orders = Order::whereIn('status', ['active', 'paid'])->with('dish', 'addons')->get();
    return response()->json($orders);
}



public function updateOrder(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
        'comment' => 'nullable|string',
    ]);

    $order = Order::findOrFail($id);
    $order->quantity = $request->input('quantity');
    $order->comment = $request->input('comment');

    $order->addons()->detach();
    if ($request->has('addons')) {
        foreach ($request->addons as $addon_id) {
            $order->addons()->attach($addon_id);
        }
    }

    $order->save();

    return redirect()->route('order.create', ['table_id' => $order->table_id])
        ->with('success', 'Order updated successfully.');
}










public function showSplitPayment($id)
{
    $table = Table::findOrFail($id);
    return view('split_payment', compact('table'));
}

public function splitPay(Request $request, $id)
{
    $validatedData = $request->validate([
        'orders' => 'required|array',
        'orders.*' => 'exists:orders,id'
    ]);

    $orders = Order::whereIn('id', $validatedData['orders'])->get();
    $totalAmount = $orders->sum('price');

    // Process the payment logic here
    // For simplicity, let's mark the selected orders as paid
    foreach ($orders as $order) {
        $order->status = 'paid';
        $order->save();
    }

    return redirect()->route('table.show', ['id' => $id])->with('success', 'Selected items paid successfully.');
}


public function showCheckout(Request $request)
{
    $tableId = $request->query('table_id');
    $dishId = $request->query('dish_id');

    $dish = Dish::findOrFail($dishId);

    $add_ons = AddOn::join('dish_add_ons', 'add_ons.id', '=', 'dish_add_ons.addon_id')
        ->where('dish_add_ons.id', $dishId) 
        ->select('add_ons.id', 'add_ons.addon_name', 'add_ons.price') 
        ->get();

    return view('checkout', compact('tableId', 'dishId', 'dish', 'add_ons'));
}



}
