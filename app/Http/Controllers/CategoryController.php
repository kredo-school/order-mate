<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $category;

    public function __construct(Category $category){
        $this->category = $category;
    }

    public function index(){
        $all_categories = $this->category->all();
        // フォルダー、ファイル名
        return view('managers.products.categories')->with([
            'all_categories' => $all_categories
        ]);
    }

    public function store (Request $request){
        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $this->category->name = $request->name;
        $this->category->save();

        // リダイレクト
        return redirect()->back();
    }

    public function update(Request $request, $id){
        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = $this->category->findOrFail($id);
        $category->name = $request->name;
        $category->save();

        // リダイレクト
        return redirect()->back();
    }

    public function destroy($id){
        $category = $this->category->findOrFail($id);
        $category->delete();

        // リダイレクト
        return redirect()->back();
    }
}
