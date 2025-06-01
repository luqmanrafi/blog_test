<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->except(['index']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        
        $data = Category::with('articles')->get();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No categories found'], 200);
        }
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only admin can create a category
        $role = Auth::user()->role_id == 1; 
        if (!$role) {
            return response()->json(['error' => 'Access denied. Can not create Category'], 403);
        };

        // Validate input
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        //cek apakaha ada kategori dengan nama yang sama
        if (Category::where('category_name', $request->category_name)->exists()) {
            return response()->json(['error' => 'Category already exists'], 409);
        }

        //create category
        try{
            Category::create([
                'category_name' => $request->category_name,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create category', 'details' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Category created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //cari berdasarkan id
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //hanya admin yg bisa update kategori
        $auth = Auth::user();
        if ($auth->role_id !== 1) {
            return response()->json(['error' => 'Access denied. Can not update category'], 403);
        }
        //validasi input
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        //mencari kategori berdasarkan id
        try {
            $category = Category::findOrFail($id);
            try{
                //cek apakah ada nama kategori yang sama dengan id yang berbeda
                $duplicate = Category::where('category_name', $request->category_name)
                    ->where('id', '!=', $id)
                    ->exists();
                if ($duplicate) {
                    return response()->json(['error' => 'Category name already exists'], 409); //kembalikan error jika ada
                }
                //update kategori
                $category->update([
                    'category_name' => $request->category_name,
                ]);
                return response()->json(['message' => 'Category updated successfully'], 200); // jika sukses update
            }
            catch (Exception $e) {
                return response()->json(['error' => 'Failed to update category', 'details' => $e->getMessage()], 500);//jika gagal update
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category id not found'], 404); //jika id tidak ditemukan
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //hanya admin yg dapat menghapus
        $auth = Auth::user();
        if ($auth->role_id != 1) {
            return response()->json(['error' => 'Access denied. Can not delete category'], 403);
        }

        try{
            //cari category berdasarkan id
            $category = Category::findOrFail($id);
            try{
                //delete data
                $deleted = $category->delete();
                if ($deleted){
                    return response()->json(['message' => 'Category deleted successfully'], 200); //response sukses
                } else {
                    return response()->json(['error' => 'Failed delete category. Try again later'], 500);
                }
                //kesalahan delete data
            } catch (\Exception $e) {
                return response()->json(['error' => 'Server error. Failed delete data', 500]);
            }
        //error id tidak ditemukan
        } catch (ModelNotFoundException $e){
            return response()->json(['error'=>'Delete failed. Category not found'],404);
        }
    }
}
