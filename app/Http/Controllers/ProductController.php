<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\CustomGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Ensure this line is present

class ProductController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();
    $all_categories = Category::where('user_id', $user->id)->get();
    $activeCategory = $all_categories->first();

    // --- 検索あり ---
    if ($request->filled('search')) {
        $keyword = $request->input('search');
        $products = Menu::where('user_id', $user->id)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->get();

        return view('managers.products.products', [
            'products' => $products,
            'all_categories' => $all_categories,
            'activeCategory' => null,
            'search' => $keyword,
        ])->with('isGuestPage', false);
    }

    // --- 通常表示 ---
    $products = $activeCategory
        ? Menu::where('user_id', $user->id)
              ->where('menu_category_id', $activeCategory->id)
              ->get()
        : collect();

    return view('managers.products.products', [
        'products' => $products,
        'all_categories' => $all_categories,
        'activeCategory' => $activeCategory,
    ])->with('isGuestPage', false);
}


    public function byCategory($id)
    {
        $userId = Auth::id();
        $products = Menu::where('menu_category_id', $id)
                        ->where('user_id', $userId)
                        ->get();

        return view('managers.products.partials.products', compact('products'))
               ->with('isGuestPage', false);
    }

    public function create()
    {
        $userId = Auth::id();
        $all_categories = Category::where('user_id', $userId)->get();
        $customGroups = CustomGroup::where('user_id', $userId)->get();

        return view('managers.products.add-product', compact('all_categories', 'customGroups'))
               ->with('isGuestPage', false);
    }

    // store
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'menu_category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'tag' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'allergens' => 'nullable|array',
            'allergens.*' => 'string',
        ]);

        // Menu 作成
        $menu = new Menu();
        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->description = $request->description;
        $menu->menu_category_id = $request->menu_category_id;
        $menu->user_id = Auth::id();
        $menu->allergy = $request->input('allergens', []); // 配列として保存


        // 画像アップロード
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu_images', 'public');
            $menu->image = $path; // DBにはパスだけ
        }

        if ($request->hasFile('tag')) {
            $path = $request->file('tag')->store('tags', 'public');
            $menu->tag = $path;
        }

        $menu->save();

        // カスタムグループの保存
        if ($request->has('custom_groups')) {
            $syncData = [];
            foreach ($request->custom_groups as $group) {
                if (!empty($group['id'])) {
                    $syncData[$group['id']] = [
                        'is_required' => isset($group['is_required']),
                        'max_selectable' => $group['max_selectable'] ?? 1,
                    ];
                }
            }
            $menu->customGroups()->sync($syncData);
        }

        return redirect()->route('manager.products.index');
    }


    // edit メソッド
    public function edit($id)
    {
        $product = Menu::findOrFail($id);
        $all_categories = Category::where('user_id', Auth::id())->get();
        $customGroups = CustomGroup::where('user_id', Auth::id())->get();

        return view('managers.products.edit-product')->with([
            'product' => $product,
            'all_categories' => $all_categories,
            'customGroups' => $customGroups,
        ]);
    }

    // update メソッド
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'menu_category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'tag' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'allergens' => 'nullable|array',
            'allergens.*' => 'string',
        ]);

        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->description = $request->description ?? '';
        $menu->menu_category_id = $request->menu_category_id;
        $menu->allergy = $request->input('allergens', []);

        // 画像更新
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu_images', 'public');
            $menu->image = $path;
        }

        if ($request->hasFile('tag')) {
            $path = $request->file('tag')->store('tags', 'public');
            $menu->tag = $path;
        }

        $menu->save();

        // カスタムグループ更新
        if ($request->has('custom_groups')) {
            $syncData = [];
            foreach ($request->custom_groups as $group) {
                if (!empty($group['id'])) {
                    $syncData[$group['id']] = [
                        'is_required' => isset($group['is_required']),
                        'max_selectable' => $group['max_selectable'] ?? 1,
                    ];
                }
            }
            $menu->customGroups()->sync($syncData);
        } else {
            $menu->customGroups()->sync([]); // 空なら削除
        }

        return redirect()->route('manager.products.index');
    }

    // destroy メソッド
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->customGroups()->detach();
        $menu->delete();

        // 削除後は一覧へ
        return redirect()->route('manager.products.index');
    }


    public function show($id)
    {
        $product = Menu::findOrFail($id);
        return view('managers.products.show', compact('product'));
    }
}
