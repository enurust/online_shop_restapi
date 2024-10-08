<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    
    public function createOrder(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $products = $request->input('products');
        $totalPrice = 0;
        $orderItems = [];

        foreach ($products as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                return response()->json([
                    'error' => "Товар с таким ID {$productData['product_id']} не найден!"
                ], 404);
            }

            if ($product->quantity < $item['quantity']) {
                return response()->json(['error' => 'Не хватает товара: ' . $product->name], 400);
            }

            $totalPrice += $product->price * $item['quantity'];

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price
            ];
        }


        if ($user->balance < $totalPrice) 
        {
            return response()->json(['error' => 'Недостаточно средств на балансе'], 400);
        }


        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'status' => 'Новый',
        ]);

        foreach ($orderItems as $item) 
        {

            $product = Product::find($item['product_id']);
            $product->quantity -= $item['quantity'];
            $product->save();

            $order->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        $user->balance -= $totalPrice;
        $user->save();

        return response()->json(['message' => 'Заказ успешно создан', 'order' => $order], 201);
    }


    public function cancelOrder($orderId)
    {
        $user = Auth::user();

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->where('status', 'Новый')
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Заказ не найден или уже отменен'], 404);
        }

        foreach ($order->products as $product) 
        {
            $productInStock = Product::find($product->id);
            $productInStock->quantity += $product->pivot->quantity;
            $productInStock->save();
        }

        $user->balance += $order->total_price;
        $user->save();

        $order->status = 'Отмененный';
        $order->save();

        return response()->json(['message' => 'Заказ отменен'], 200);
    }

    public function getUserOrders()
    {
        $user = Auth::user();
        $orders = Order::with('products')->where('user_id', $user->id)->get();

        if($orders->isEmpty())
        {
            return response()->json(['message' => 'У вас нет заказов'], 400);
        }
        else
        {
            return response()->json($orders, 200);
        }

    }

     public function getAllOrders()
    {
        $orders = Order::with('user', 'products')->orderByDesc('created_at')->get();
        return response()->json($orders, 200);
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $validStatuses = ['Новый', 'Подтвержденный', 'Отмененный'];

        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'error' => 'Заказ не найден.'
            ], 404);
        }

        $status = $request->input('status');
        if (!$status) {
            return response()->json([
                'error' => 'Необходимо указать статус.'
            ], 400);
        }

        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'error' => 'Недопустимый статус. Возможные значения: ' . implode(', ', $validStatuses),
            ], 400); 
        }

        $order->status = $status;
        $order->save();

        return response()->json([
            'message' => 'Статус заказа успешно обновлен.',
            'order' => $order
        ], 200);
    }
}
