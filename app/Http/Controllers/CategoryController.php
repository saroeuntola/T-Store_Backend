<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
class CategoryController extends Controller
{
      public function __construct(){

        $this->middleware('permission:category-create|category-edit|category-delete', ['only' => ['store','update','destroy']]);
        $this->middleware('permission:category-create', ['only' => ['store']]);
        $this->middleware('permission:category-edit', ['only' => ['update']]);
        $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }
    public function index(){
            try{
                $category = Category::with("getUser")->get();
                return response()->json([
                   'status' => 'success',
                    'category' => $category
                ]);
            }
            catch (\Exception $e){
                return response()->json([
                   'status' => 'error',
                   'message' => $e->getMessage()
                ]);
            }
    }

    public function store(Request $request){
        try{
            $category = Category::create($request->all());
            return response()->json([
               'status' => 'success',
               'message' => 'Category created successfully',
                'category' => $category
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
        try{
            $category = Category::with("getUser")->find($id);
            if($category){
                return response()->json([
                   'status' => 'success',
                   'message' => 'Category found',
                    'category' => $category
                ]);
            }
            else{
                return response()->json(['message'=>'Category not found']);
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
        try{
            $category = Category::find($id);
            if($category){
                $category->update($request->all());
                return response()->json([
                   'status' => 'success',
                   'message' => 'Category updated successfully',
                    'category' => $category
                ]);
            }
            else{
                return response()->json(['message'=>'Category not found']);
            }
        }
        catch (\Exception $e){
            return response()->json([
               'status' => 'error',
               'message' => $e->getMessage()
            ]);
        }
    }
    public function destroy($id){
        try{
            $category = Category::find($id);
            if($category){
                $category->delete();
                return response()->json([
                   'status' => 'success',
                   'message' => 'Category deleted successfully'
                ]);
            }
            else{
                return response()->json(['message'=>'Category not found']);
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
