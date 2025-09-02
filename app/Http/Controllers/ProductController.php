<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\CustomGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $all_categories = Category::where('user_id', $userId)->get();
        $initialCategory = $all_categories->first();

        $products = $initialCategory
            ? Menu::where('menu_category_id', $initialCategory->id)->where('user_id', $userId)->get()
            : collect();

        // redirect じゃなく view を返す！
        return view('managers.products.products', compact('all_categories', 'products'));
    }


    public function byCategory($id)
    {
        $userId = Auth::id();

        $products = Menu::where('menu_category_id', $id)->where('user_id', $userId)->get();

        return view('managers.products.partials.products', compact('products'));
    }


    public function create()
    {
        $userId = Auth::id();

        $all_categories = Category::where('user_id', $userId)->get();
        $customGroups = CustomGroup::where('user_id', $userId)->get();
        // フォルダー、ファイル名
        return view('managers.products.add-product')->with([
            'all_categories' => $all_categories,
            'customGroups' => $customGroups,
        ]);
    }

    // store
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'menu_category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image',
            'tag' => 'nullable|image',
        ]);

        // Menu 作成
        $menu = new Menu();
        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->description = $request->description;
        $menu->menu_category_id = $request->menu_category_id;
        $menu->user_id = Auth::id();
        $menu->allergens = $request->allergens ?? [];



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
            'image' => 'nullable|image',
            'tag' => 'nullable|image',
        ]);

        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->description = $request->description ?? '';
        $menu->menu_category_id = $request->menu_category_id;
        $menu->allergens = $request->allergens ?? [];


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
