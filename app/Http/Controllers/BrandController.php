<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
  public function __construct(){

        $this->middleware('permission:brand-create|brand-edit|brand-delete', ['only' => ['store','update','destroy']]);
        $this->middleware('permission:brand-create', ['only' => ['store']]);
        $this->middleware('permission:brand-edit', ['only' => ['update']]);
        $this->middleware('permission:brand-delete', ['only' => ['destroy']]);
    }
    public function index(){
        try{
            $brand = Brand::all();
            return response()->json([
               'status' => 'success',
                'brand' => $brand
            ]);
        } catch (\Exception $e) {
            return response()->json([
               'status' => 'error',
                'message' => 'Error fetching brands'
            ], 500);
        }
    }

    public function store(Request $request){
        try {
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|string',
            'brand_image' => 'nullble|image|mimes:jpeg,png,jpg,gif',
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    $brandImage =[
            'brand_name' => $request->brand_name,
            'user_id' => $request->user_id,
        ];

       if ($request->hasFile('banner_image')) {
            $image = $request->file('brand_image');
            if ($request->has('old_image') && $request->old_image) {
                $oldImagePath = storage_path("app/public/brand_image/") . $request->old_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            $image->storeAs(storage_path('public/brand_image/'), $imageName);
            $brandImage['banner_image'] = $imageName;
        }

        $brand = Brand::create($brandImage);
        return response()->json([
           'status' => 'success',
            'brand' => $brand
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
    }

    public function update(Request $request, $id){
              try {
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|string',
            'brand_image' => 'nullble|image|mimes:jpeg,png,jpg,gif',
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $brandImage =[
            'brand_name' => $request->brand_name,
            'user_id' => $request->user_id,
        ];
       if ($request->hasFile('banner_image')) {
            $image = $request->file('brand_image');
            if ($request->has('old_image') && $request->old_image) {
                $oldImagePath = storage_path("app/public/brand_image/") . $request->old_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            $image->storeAs(storage_path('public/brand_image/'), $imageName);
            $brandImage['banner_image'] = $imageName;
        }
        $brand = Brand::findOrFail($$id);
        $brand->update($brandImage);
        return response()->json([
           'status' => 'success',
            'brand' => $brand
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
    }

    public function show($id){
        try {
            $brand = Brand::with('getUser')->find($id);
            if (!$brand) {
                return response()->json([
                   'message' => 'Brand not found.',
                ], 404);
            }
            return response()->json([
               'status' => 'success',
                'brand' => $brand
            ]);
        } catch (\Exception $e) {
            return response()->json([
               'status' => 'error',
               'message' => 'Error fetching brand'
            ], 500);
        }
    }
    public function destroy($id){
        try {
            $brand = Brand::find($id);
            if (!$brand) {
                return response()->json([
                   'message' => 'Brand not found.',
                ], 404);
            }
            $brand->delete();
            return response()->json([
               'status' => 'success',
               'message' => 'Brand deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
               'status' => 'error',
               'message' => $e->getMessage()
            ], 500);
        }
    }
}
