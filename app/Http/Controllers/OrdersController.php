<?php

namespace App\Http\Controllers;

use App\Models\Orders;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    /**
     * Store a new order in the database.
     */

     public function index(){
        try{

            $orders = Orders::with('getUser','getProduct')->get();
            return response()->json(['orders' => $orders], 200);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
     }
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'qty' => 'required|integer',
            'color' => 'required|array',
            'color.*' => 'string',
            'size' => 'required|array',
            'size.*' => 'string',
        ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $order = Orders::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'total_price' => $request->total_price,
                'color' => $request->color,
                'size' => $request->size// Store size as JSON
            ]);

            return response()->json(['message' => 'Order placed successfully!', 'order' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id){
        try{
            // Retrieve a single order
            $order = Orders::with('getUser','getProduct')->find($id);
            if($order){
                return response()->json(['message'=>'success', 'order'=>$order]);
            }
            else{
                return response()->json(['message'=>'Order not found'], 404);
            }
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id){
        try{
     
            $order = Orders::find($id);
            if($order){
                $order->update($request->all());
                return response()->json(['message'=>'Order updated successfully', 'order'=>$order]);
            }
            else{
                return response()->json(['message'=>'Order not found'], 404);
            }
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id){
        try{
            // Delete an existing order
            $order = Orders::find($id);
            if($order){
                $order->delete();
                return response()->json(['message'=>'Order deleted successfully']);
            }
            else{
                return response()->json(['message'=>'Order not found'], 404);
            }
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}


