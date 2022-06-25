<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;

class CartController extends Controller
{
    public function index()
    {
        $categories = Category::getAll();
        if (Auth::check())
        {
            $cart_items = Auth::user()->cart_products()->with('product')->whereNull('order_id')->get()->keyBy('product_id');
            $products = array_map('array_merge', $cart_items->toArray(), array_map(function($obj){ return array_diff_key($obj['product'],['quantity' => 0]); }, $cart_items->toArray()));
            $cart = ['products' => $products, 'total' => $cart_items->sum(function($t){ return $t->quantity * $t->product->price; })];
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart['products'])) {
                $products = Product::findMany(array_keys($cart['products']))->keyBy('id')->toArray();
                $cart['products'] = array_map('array_merge', $products, $cart['products']);
            }
        }

        $steps = [
            ['name'=>'Shopping cart (' . (isset($cart['products']) ? count($cart['products']) : 0) .')','link'=>'','active'=>true],
            ['name'=>'Billing & Shipping', 'link' => 'info'],
            ['name'=>'Payment'],
            ['name'=>'Order Summary']
        ];
        return view('cart.overview', compact('cart','steps','categories'));
    }

    public function info()
    {
        if (Auth::check())
        {
            $cart_items = Auth::user()->cart_products()->with('product')->whereNull('order_id')->get()->keyBy('product_id');
            $products = array_map('array_merge', $cart_items->toArray(), array_map(function($obj){ return array_diff_key($obj['product'],['quantity' => 0]); }, $cart_items->toArray()));
            $cart = ['products' => $products, 'total' => $cart_items->sum(function($t){ return $t->quantity * $t->product->price; })];
        } else {
            $cart = session()->get('cart', ['products' => []]);
        }
        $steps = [
            ['name'=>'Shopping cart (' . (isset($cart['products']) ? count($cart['products']) : 0) .')','link'=>''],
            ['name'=>'Billing & Shipping', 'link' => 'info', 'active'=>true],
            ['name'=>'Payment'],
            ['name'=>'Order Summary']
        ];
        $shipping = [
            ['name' => 'Usual mail', 'price' => 5, 'duration' => '7'],
            ['name' => 'Priority mail', 'price' => 17, 'duration' => '2-3'],
            ['name' => 'Express courier', 'price' => 20, 'duration' => '1'],
            ['name' => 'Self-delivery', 'price' => 0]
        ];

        if (isset($cart['products']) && count($cart['products']) > 0) {
            $categories = Category::getAll();
            return view('cart.info', compact('cart','steps','shipping','categories'));
        } else {
            return redirect('/cart');
        }
    }

    public function add($id, Request $request)
    {
        $product = Product::findOrFail($id);
        $quantity = $request->input('quantity', 1);

        if ($quantity < 0) return redirect()->back();

        if (Auth::check()) {
            Auth::user()->cart_products()->create(['product_id' => $product->id, 'quantity' => $quantity]);
        } else {
            $cart = session()->get('cart', ['products'=>[], 'total' => 0]);
            $cart['products'][$id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price
            ];
            $cart['total'] += $quantity * $product->price;
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $quantity = $request->input('quantity');
        
        // Validation of the inputs
        if (is_null($id) || is_null($quantity) || $quantity < 0) return redirect()->back();
        
        // For logged-in user change the quantity of the item in the database
        if (Auth::check()) {
            $cart_item = Auth::user()->cart_products()->whereNull('order_id')->where('product_id',$id)->first();
            if (!is_null($cart_item)) {
                if ($quantity == 0) {
                    $cart_item->forceDelete();
                } else {
                    $cart_item->quantity = $quantity;
                    $cart_item->save();
                }
            }   
        // For guests 
        } else {
            $cart = session()->get('cart', ['products'=>[], 'total' => 0]);
            if(isset($cart['products'][$id])) {
                $cart['total'] += ($quantity - $cart['products'][$id]['quantity']) * $cart['products'][$id]['price'];
                $cart['products'][$id]['quantity']=$quantity;
                if ($quantity == 0) {
                    unset($cart['products'][$id]);
                }
            }
            if (count($cart['products']) == 0) {
                session()->forget('cart');
            } else {
                session()->put('cart',$cart);
            }
        }

        return redirect()->back();
    }

    public function remove($id)
    {
        if (Auth::check()) {
            $cart_item = Auth::user()->cart_products()->whereNull('order_id')->where('product_id',$id)->first();
            if (!is_null($cart_item)) {
                $cart_item->forceDelete();
            }
        } else {
            $cart = session()->get('cart', ['products'=>[], 'total' => 0]);
    
            if (is_null($id)) return redirect()->back();
            
            if(isset($cart['products'][$id])) {
                $cart['total'] -= $cart['products'][$id]['quantity'] * $cart['products'][$id]['price'];
                unset($cart['products'][$id]);
            } 
    
            if (count($cart['products']) == 0) {
                session()->forget('cart');
            } else {
                session()->put('cart',$cart);
            }
        }
        return redirect()->back();
    }

    public function fillInfo(Request $request)
    {
        $rules = [
            'firstName' => 'required|alpha',
            'lastName' => 'required|alpha',
            'street' => 'required',
            'apt' => 'required',
            'city' => 'required',
            'country' => 'required',
            'phone' => 'required|numeric',
            'zip' => 'required',
            'shipping' => 'required',
            'payment' => 'required',
            'terms' => 'accepted',
            'privacy' => 'accepted',
        ];

        if (!Auth::check()) {
            $rules['email'] = 'required|email';
            $email = $request->input('email');
            $cart = session()->get('cart', []);
            if (isset($cart['products'])) {
                $ids = array_map(function($arr){ return ['product_id' => $arr];},array_keys($cart['products']));
                $quantities = array_map(function($arr){ return ['quantity' => $arr['quantity']];},array_values($cart['products']));
                $cart_items = array_map('array_merge',$ids, $quantities);
                $subtotal = $cart['total'];
            }
        } else {
            $email = Auth::user()->email;
            $cart_items = Auth::user()->cart_products()->with('product')->whereNull('order_id')->get();
            $subtotal = $cart_items->sum(function($t){ return $t->quantity * $t->product->price; });
        }

        $request->validate($rules);
        
        $shipping = [
            'Usual mail' => 5,
            'Priority mail' => 17,
            'Express courier' => 20,
            'Self-delivery' => 0
        ];

        $order = Order::create([
            'shipping_price' => $shipping[$request->input('shipping')],
            'payment_price' => $request->input('payment') == 'Cash' ? 1 : 0,
            'total' => $subtotal + $shipping[$request->input('shipping')] + ($request->input('payment') == 'Cash' ? 1 : 0),
            'shipping_method' => $request->input('shipping'),
            'payment_method' => $request->input('payment'),
            'email' => $email,
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName'),
            'phone_number' => $request->input('phone'),
            'street' => $request->input('street'),
            'apartment' => $request->input('apt'),
            'city' => $request->input('city'),
            'zip_code'=> $request->input('zip'),
            'country' => $request->input('country')
        ]);

        if (Auth::check()) {
            $order->cart_products()->saveMany($cart_items);
        } else {
            $request->session()->forget('cart');
            $order->cart_products()->createMany($cart_items);
        }

        foreach($order->cart_products()->get() as $cart_product) {
            $cart_product->product->sold += $cart_product->quantity;
            $cart_product->product->save();
        }

        return redirect('/cart/summary/'.$order->id);
    }

    public function summary($id) {
        $steps = [
            ['name'=>'Shopping cart','link'=>''],
            ['name'=>'Billing & Shipping'],
            ['name'=>'Payment'],
            ['name'=>'Order Summary','active'=>true]
        ];
        $categories = Category::getAll();
        $order = Order::where('id',$id)->with('cart_products')->first();
        return view('cart.summary', compact('order','steps','categories'));
    }
}