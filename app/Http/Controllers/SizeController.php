<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
{
     public function index(){

        try{
            $size = Size::with("getUser")->get();
            return response()->json([
               'status' => 'success',
                'size' => $size
            ]);
        }
        catch (\Exception $e){
            return response()->json([
               'status' => 'error',
               'message' => $e->getMessage()
            ]);
        }
     }

     public function store(Request $request)
    {

        try{

            $validate = Validator::make($request->all(),[
                'size_name' => 'required|string',
                'user_id' => 'required|integer'
            ]);
            if ($validate->fails()){
                return response()->json(['error' => $validate->errors()], 400);
            }
            $size = Size::create($request->all());
            return response()->json([
               'status' => 'success',
               'message' => 'Size created successfully',
                'size' => $size
            ]);
        }
        catch (\Exception $e){
            return response()->json([
               'status' => 'error',
               'message' => $e->getMessage()
            ]);
        }

    }

    public function show($id){
        try {
             $size = Size::with("getUser")->find($id);
            if (!$size) {
                return response()->json([
                   'message' => 'Size not found.',
                ], 404);
            }

            return response()->json([
               'message' => 'Size retrieved successfully!',
               'size' => $size,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id){

       try{

        $validate = Validator::make($request->all(),[
                'size_name' => 'required|string',
                'user_id' => 'required|integer'
            ]);
            if ($validate->fails()){
                return response()->json(['error' => $validate->errors()], 400);
            }
            $size = Size::find($id);
            if (!$size) {
                return response()->json([
                   'message' => 'Size not found.',
                ], 404);
            }
            $size->update($request->all());
            return response()->json([
               'message' => 'Size updated successfully!',
                'size' => $size,
            ], 200);
       }
        catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);

        }


    }

    public function destroy($id){
        try {
            $size = Size::find($id);
            if (!$size) {
                return response()->json([
                   'message' => 'Size not found.',
                ], 404);
            }

            $size->delete();

            return response()->json([
               'message' => 'Size deleted successfully!',
            ], 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
