<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    private $category;

    public function __construct(Category $category){
        $this->category = $category;
    }

    public function index(){
        // ログインユーザーのstore_idで絞り込む
        $storeId = Auth::user()->store_id;
        $all_categories = $this->category->where('store_id', $storeId)->get();

        return view('managers.products.categories')->with([
            'all_categories' => $all_categories
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $storeId = Auth::user()->store_id;

        $this->category->create([
            'name' => $request->name,
            'store_id' => $storeId,
        ]);

        return redirect()->back()->with('success', 'Category created!');
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $storeId = Auth::user()->store_id;

        $category = $this->category
            ->where('store_id', $storeId)
            ->findOrFail($id);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Category updated!');
    }

    public function destroy($id){
        $storeId = Auth::user()->store_id;

        $category = $this->category
            ->where('store_id', $storeId)
            ->findOrFail($id);

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted!');
    }
}
