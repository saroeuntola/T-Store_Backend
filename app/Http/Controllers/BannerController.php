<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
   public function __construct()
    {
        $this->middleware('permission:banner-create|banner-edit|banner-delete', ['only' => ['update', 'store', 'destroy']]);
        $this->middleware('permission:banner-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:banner-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:banner-delete', ['only' => ['destroy']]);
    }

    public function BannerCount(){

        $count = Banner::count();
        return response()->json([
        'count' => $count
        ]);

    }
    public function index(){
        try{
            $banner = Banner::with("getUser")->get();
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
            'title' => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'description' => 'required|string',
            'link' => 'required|string',
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $bannerImage = [
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'user_id' => $request->user_id,
        ];

        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');

            // Delete old image if exists
            if ($request->has('old_image') && $request->old_image) {
                $oldImagePath = storage_path("app/public/banner_image/") . $request->old_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Store new image
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/banner_image/', $imageName);
            $bannerImage['banner_image'] = $imageName;
        }

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
            'title' => 'required|string',
           'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'description' => 'required|string',
            'link' => 'required|string',
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $bannerImage =[
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'user_id' => $request->user_id,
        ];

       if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');

            // Delete old image if exists
            if ($request->has('old_image') && $request->old_image) {
                $oldImagePath = storage_path("app/public/banner_image/") . $request->old_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Store new image
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/banner_image/', $imageName);
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
            $banner = Banner::with('getUser')->find($id);
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
