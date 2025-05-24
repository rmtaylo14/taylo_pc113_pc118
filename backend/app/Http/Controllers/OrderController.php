<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // List all orders
    public function index()
    {
        return response()->json(Order::with('items')->get());
    }


    public function deliveries() //cccccccccccccccccccccccccccccccckerkerrrrrrrr
    {
        return Order::with(['items', 'user'])
            ->where('status', 'to be delivered')
            ->get();
    }


    // Store a new order
    public function store(Request $request)
    {
        $user = $request->user(); // Get authenticated user

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'integer|exists:menu_items,id',
        ]);

        // Create the order with status 'pending'
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Attach items to order (pivot)
        $order->items()->attach($data['items']);

        // Load items relationship for response
        $order->load('items');

        return response()->json($order, 201);
    }

    // Show specific order with items loaded
    public function show(Order $order)
    {
        $order->load('items');
        return response()->json($order);
    }

    // Update order (optional, keep your existing update logic if needed)
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'sometimes|string|in:pending,completed,cancelled',
            // you may add more fields if you want to allow updates
        ]);

        $order->update($data);
        $order->load('items');

        return response()->json($order);
    }


    public function deliveriesGroupedByUser()
{
    $orders = Order::with(['items', 'user'])
        ->whereIn('status', ['pending', 'to_be_delivered']) // Make sure this matches updateStatus!
        ->get();

    $grouped = $orders->groupBy('user_id')->map(function ($userOrders) {
        $firstOrder = $userOrders->first();

        if (!$firstOrder || !$firstOrder->user) {
            return null;
        }

        $user = $firstOrder->user;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->firstname . ' ' . $user->lastname,
                'address' => $user->address ?? 'N/A',
                'phone_number' => $user->phone_number ?? 'N/A',
            ],
            'orders' => $userOrders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'price' => $item->price
                        ];
                    }),
                    'created_at' => $order->created_at->toDateTimeString(),
                ];
            })->values()
        ];
    })->filter()->values();

    return response()->json($grouped);
}




    public function updateStatus(Request $request, $id)
    {
        // Find the order
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Update status to "to_be_delivered"
        $order->status = 'to_be_delivered';
        $order->save();

        return response()->json(['message' => 'Order status updated successfully', 'order' => $order]);
    }



    public function userOrders(Request $request)
    {
        $user = $request->user();

        $orders = $user->orders()->with('items')->get()->map(function ($order) {
            $total = $order->items->sum(function ($item) {
                return $item->price;
            });

            return [
                'id' => $order->id,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'total' => $total,
            ];
        });

        return response()->json($orders);
    }


    // Delete order
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully.']);
    }
}
