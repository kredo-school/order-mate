<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\CustomGroup;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $all_categories = Category::all();
        $initialCategory = $all_categories->first();

        // カテゴリが無い/メニューが無い場合でも必ずコレクションを渡す
        $products = $initialCategory
            ? Menu::where('menu_category_id', $initialCategory->id)->get()
            : collect();

        return view('managers.products.products', compact('all_categories', 'products'));
    }

    public function byCategory($id)
    {
        $products = Menu::where('menu_category_id', $id)->get(); // 0件でもコレクション
        return view('managers.products.partials.products', compact('products'));
    }

    public function create()
    {
        $all_categories = Category::all();
        $customGroups = CustomGroup::all();
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
        $menu->store_id = auth()->user()->store->id;


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

        return redirect()->route('manager.index');
    }


    // update
    // edit メソッド
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $all_categories = Category::all();
        $customGroups = CustomGroup::all();

        return view('managers.products.edit-product')->with([
            'menu' => $menu,
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
        $menu->customGroups()->detach(); // 中間テーブルも削除
        $menu->delete();

        return redirect()->route('manager.products.index');
    }
}
