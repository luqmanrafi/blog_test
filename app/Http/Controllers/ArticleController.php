<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //validasi limit
        $validate = $request->validate([
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        $limit = $validate['limit'] ?? 10;

        $data = Article::with('category')->latest()->paginate($limit);
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No articles found'], 404);
        }
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Hanya admin(role_id = 1) dan author(role_id = 2) yg dapat create data artikel
        $role = Auth::user()->role_id == 1 || Auth::user()->role_id == 2;
        if (!$role) {
            return response()->json(['error' => 'Access denied. Can not create article'], 403);
        }

        //validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        //create article
        try {
            $data = Article::create([
                'title' => $request->title,
                'content' => $request->content,
                'author' => Auth::user()->name,
                'category_id' => $request->category_id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create article', 'details' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Article created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = Article::find($id);
        if (!$data) {
            return response()->json(['message' => 'Article not found'], 404);
        }
        return response()->json($data, 200);
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
        //cek role user
        $isAdmin = Auth::user()->role_id = 1;
        $isAuthor = Auth::user()->role_id = 2;
        if (!$isAdmin or !$isAuthor) {
            return response()->json(['error' => 'Access denied. Can not update article'], 403);
        }

        //validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try{
            $article = Article::findOrFail($id);
            try{
                $article->update();
            } catch(Exception $e){
                return response()->json(['Error' => 'Update failed.', $e->getMessage()], 500);
            }
        } catch(ModelNotFoundException $e){
            return response()->json(['Error' => 'Update failed. Article id not found'], 404);
        }
        return response()->json(['message' => 'Article updated successfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //autentikasi role user
        $role = Auth::user()->role_id;
        if (!$role == 1 or !$role == 2) {
            return response()->json(['error' => 'Access denied. Can not delete article'], 403);
        }

        //cari id article
        try {
            $article = Article::findOrFail($id);
            try {
                $article->delete();
                return response()->json(['message' => 'Category deleted succesfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Delete failed. try again later'], 500);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Delete failed. Article not found'], 404);
        }
    }

    public function search(Request $request){
        $validatedData = $request->validate([
                'category_id' => 'nullable|exists:categories,id',
                'keyword' => 'nullable|string|max:255',
         ]);
        $query = Article::query();


        if (!empty($validatedData['category_id'])) {
            $query->where('category_id', $request->category_id);
        }
        if (!empty($validatedData['keyword'])) {
            $query->where('title', 'like', '%' . $request->keyword . '%')
              ->orWhere('content', 'like', '%' . $request->keyword . '%');
        }

        $articles = $query->with('category')->paginate(10);
        
        if ($articles->isEmpty()) {
            return response()->json(['message' => 'No articles found'], 404);
        }
        return response()->json($articles, 200);
    }

}
