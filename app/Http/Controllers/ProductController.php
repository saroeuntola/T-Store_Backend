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
      public function ProductCount(){

        $count = Product::count();
        return response()->json([
        'count' => $count
        ]);
    }
public function index(Request $request){
    try {
        $limit = $request->input('limit');

        $query = Product::with('getCategory', 'getUser', 'sizes', 'colors');


        if ($limit) {
            $query->take($limit);
        }

        $products = $query->get();

        return response()->json([
            'message' => 'success',
            'product' => $products
        ]);
    }
    catch (\Exception $ex) {
        return response()->json([
            'message' => 'error',
            'error' => $ex->getMessage()
        ]);
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
                $oldImagePath = storage_path('app/public/product_image/')  . $request->old_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/product_image', $imageName);
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


public function update(Request $request, $id)
{
    try {
        // Validate request
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
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
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
            if ($product->image) {
                $oldImagePath = storage_path('app/public/product_image/') . $product->image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/product_image', $imageName);
            $productData['image'] = $imageName;
        }


        $product->update($productData);

        if ($request->has('size_id')) {
            $product->sizes()->sync($request->size_id);
        }

        if ($request->has('color_id')) {
            $product->colors()->sync($request->color_id);
        }
        $product->load('sizes', 'colors');

        return response()->json([
            'message' => 'Updated successfully',
            'product' => $product
        ], 200);
    } catch (\Exception $ex) {
        return response()->json(['message' => 'error', 'error' => $ex->getMessage()], 500);
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

public function destroy($id){
    try{
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
               'message' => 'Product not found.',
            ], 404);
        }
        $product->delete();
        return response()->json([
           'message' => 'Product deleted successfully',
        ], 204);

    }
    catch(\Exception $ex){
        return response()->json(['message'=>'error', 'error'=>$ex->getMessage()]);
    }
}

   public function updateStatus($id)
{
    try {
        $user = Product::find($id);
        if ($user) {
            $newStatus = $user->status === 'Instock' ? 'Outstock' : 'Instock';
            $user->status = $newStatus;
            $user->save();
            return response()->json(['message' => 'Product status updated.']);
        } else {
            return response()->json(['message' => 'Product not found.'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 400);
    }
}


}






