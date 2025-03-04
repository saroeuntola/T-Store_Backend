<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{

    public function __construct(){
        $this->middleware('permission:color-list|color-create|color-edit|color-delete', ['only' => ['index','store','update','destroy']]);
        $this->middleware('permission:color-create', ['only' => ['store']]);
        $this->middleware('permission:color-edit', ['only' => ['update']]);
        $this->middleware('permission:color-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        try {
            $colors = Color::with("getUser")->get();
   return response()->json([
            'message' => 'successfully!',
            'color' => $colors,
        ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving colors.',
            ], 500);
        }
    }
     public function store(Request $request)
    {

       try{
        $validator = Validator::make($request->all(), [
            'color_name' => 'required|string',
            'user_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $colors = Color::create($request->all());

        return response()->json([
            'message' => 'Sizes stored successfully!',
            'color' => $colors,
        ], 201);
       }
       catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function show($id)
    {
        try {
            $color = Color::with("getUser")->find($id);
            if (!$color) {
                return response()->json([
                    'message' => 'Color not found.',
                ], 404);
            }
            return response()->json([
               'message' => 'Color retrieved successfully!',
                'color' => $color,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving color.',

            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $validator = Validator::make($request->all(), [
                'color_name' => 'required|string',
                'user_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
            $color = Color::find($id);
            if (!$color) {
                return response()->json([
                   'message' => 'Color not found.',
                ], 404);
            }
            $color->update($request->all());
            return response()->json([
               'message' => 'Color updated successfully!',
                'color' => $color,
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $color = Color::find($id);
            if (!$color) {
                return response()->json([
                    'message' => 'Color not found.',
                ], 404);
            }
            $color->delete();
            return response()->json([
                'message' => 'Color deleted successfully.',
                'color' => $color,
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting color.',
            ], 500);
        }
    }
}
