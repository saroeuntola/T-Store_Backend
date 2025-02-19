<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct(){

        $this->middleware('permission:product-create|product-edit|product-delete', ['only' => ['store','update','destroy']]);
        $this->middleware('permission:product-create', ['only' => ['store']]);
        $this->middleware('permission:product-edit', ['only' => ['update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    public function index(){
        try{
            $products = Product::with('getCategory','getUser','sizes', 'colors')->get();
            return response()->json(['message'=>'success', 'product'=>$products]);
        }
        catch(\Exception $ex){

            return response()->json(['message'=>'error', 'error'=>$ex->getMessage()]);
        }
    }

public function store(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'nullable|image',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|integer',
            'user_id' => 'required|integer',
            'size_id' => 'required|array',
            'color_id' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $productData = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'user_id' => $request->user_id,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if ($request->has('old_image') && $request->old_image) {
                $oldImagePath = storage_path('product_image') . '/' . $request->old_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('product_image'), $imageName);
            $productData['image'] = $imageName;
        }
        $product = Product::create($productData);
        $product->sizes()->attach($request->size_id);
        $product->colors()->attach($request->color_id);


        $product->load('sizes', 'colors');


        return response()->json([
            'message' => 'Created successfully',
            'product' => $product
        ], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function update(Request $request, Product $product){
    try{

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'image' => 'nullable|image',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'category_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            'size_id' => 'nullable|array',
            'color_id' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        $productData = [];
        if ($request->hasFile('image')) {
            $image = $request->file('image');


            if ($request->has('old_image') && $request->old_image) {
                $oldImagePath = storage_path('product_image'). '/' . $request->old_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = time(). '.'. $image->getClientOriginalExtension();
            $image->move(storage_path('product_image'), $imageName);
            $productData['image'] = $imageName;
        }

        $product->update($productData);


        if ($request->has('size_id') && $request->size_id) {
            $product->sizes()->sync($request->size_id);
        }
        if ($request->has('color_id') && $request->color_id) {
            $product->colors()->sync($request->color_id);
        }
        $product->load('sizes', 'colors');
        return response()->json([
           'message' => 'Updated successfully',
            'product' => $product
        ], 200);
    }
    catch(\Exception $ex){
        return response()->json(['message'=>'error', 'error'=>$ex->getMessage()]);
    }
}
public function show ($id){
    try{
        $product = Product::with('getCategory','getUser','sizes', 'colors')->find($id);
        if($product){
            return response()->json(['message'=>'success', 'product'=>$product]);
        }
        else{
            return response()->json(['message'=>'Product not found']);
        }
    }
    catch(\Exception $ex){
        return response()->json(['message'=>'error', 'error'=>$ex->getMessage()]);
    }
}

public function destroy(Product $product){
    try{
        $product->delete();
        return response()->json(['message'=>'Product deleted successfully']);
    }
    catch(\Exception $ex){
        return response()->json(['message'=>'error', 'error'=>$ex->getMessage()]);
    }
}




}






