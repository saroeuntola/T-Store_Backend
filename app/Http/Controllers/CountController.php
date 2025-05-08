<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountController extends Controller
{

    public function index(){
        $user = DB::table('users')->count();
        $product = DB::table('product')->count();
        $banner = DB::table('banner')->count();
        $order = DB::table('order')->count();
        $category = DB::table('category')->count();
        $sizes = DB::table('size')->count();
        $colors = DB::table('color')->count();

        return response()->json([
            'status' => 'success',
            'total_users' => $user,
            'total_products' => $product,
            'total_banners' => $banner,
            'total_orders' => $order,
            'total_category' => $category,
            'total_sizes' => $sizes,
            'total_colors' => $colors,
            // 'last_registered_user' => User::latest()->first(),
            // 'last_updated_product' => \App\Models\Product::latest()->first(),
            // 'last_updated_banner' => \App\Models\Banner::latest()->first(),

        ]);
    }
}

