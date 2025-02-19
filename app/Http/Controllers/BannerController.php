<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{

    public function index(){
        try{
            $banner = Banner::all();
            return response()->json([
               'status' => 'success',
                'banner' => $banner
            ]);
        } catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

       if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');

            // Delete old image if it exists
            if ($request->has('old_image') && $request->old_image) {
                $oldImagePath = storage_path('banner_image'). '/' . $request->old_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            $image->move(storage_path('banner_image'), $imageName);
            $bannerImage['banner_image'] = $imageName;
        }
        // Update the product
        $banner = Banner::create($bannerImage);
        return response()->json([
           'status' => 'success',
            'banner' => $banner
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function destroy($id){
        try{
            $banner = Banner::find($id);
            if($banner){
                $banner->delete();
                return response()->json(['message'=>'Banner deleted successfully']);
            }
            else{
                return response()->json(['message'=>'Banner not found']);
            }
        }
        catch (\Exception $e){
            return response()->json([
               'status' => 'error',
               'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id){
        try {
        $validator = Validator::make($request->all(), [
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

       if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');

            // Delete old image if it exists
            if ($request->has('old_image') && $request->old_image) {
                $oldImagePath = storage_path('banner_image'). '/' . $request->old_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            $image->move(storage_path('banner_image'), $imageName);
            $bannerImage['banner_image'] = $imageName;
        }
        $banner = Banner::find($id);
        $banner->update($bannerImage);
        return response()->json([
           'status' => 'success',
            'banner' => $banner
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }

    }

    public function show($id){
        try{
            $banner = Banner::find($id);
            if($banner){
                return response()->json([
                   'status' => 'success',
                   'message' => 'Banner found',
                    'banner' => $banner
                ]);
            }
            else{
                return response()->json(['message'=>'Banner not found']);
            }
        }
        catch (\Exception $e){
            return response()->json([
               'status' => 'error',
               'message' => $e->getMessage()
            ]);
        }
    }
}
