<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Category;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        $categories = Category::getAll();
        return view('auth.login', compact('categories'));
    }

    public function register()
    {
        $categories = Category::getAll();
        return view('auth.register', compact('categories'));
    }

    public function login_form(Request $request)
    {
        $request->validate([
            'username' => 'required|alpha_num',
            'password' => 'required|min:6',
        ]);
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $cart = session()->get('cart', []);
            if (isset($cart['products'])) {
                $ids = array_map(function($arr){ return ['product_id' => $arr];},array_keys($cart['products']));
                $quantities = array_map(function($arr){ return ['quantity' => $arr['quantity']];},array_values($cart['products']));
                $cart_items = array_map('array_merge',$ids, $quantities);
                Auth::user()->cart_products()->createMany($cart_items);
            }
            
            $request->session()->regenerate();

            if (Auth::user()->is_admin)
            return redirect()->intended('/admin')
                        ->withSuccess('Successfully logged in.');
            else
            return redirect()->intended('/')
                        ->withSuccess('Successfully logged in.');
        }
        throw ValidationException::withMessages(['user' => "Username or password is not correct."]);
    }

    public function register_form(Request $request)
    {
        $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'username' => 'required|alpha_num|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);
        
        $user = User::create([
            'email' => strtolower($request->input('email')),
            'password' => bcrypt($request->input('password')),
            'first_name' => $request->input('firstName'),
            'last_name'=> $request->input('lastName'),
            'username' => $request->input('username')
        ]);
        return redirect("/login")->withSuccess('Successfully registered! Please, log in.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}