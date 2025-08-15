<?php

namespace App\Http\Controllers;

use App\Models\CustomOption;
use App\Models\CustomGroup;
use Illuminate\Http\Request;

class CustomController extends Controller{

    private $customGroup;
    private $customOption;

    public function __construct(CustomGroup $customGroup, CustomOption $customOption){
        $this->customGroup = $customGroup;
        $this->customOption = $customOption;
    }

    public function index(){
        $all_customGroups = $this->customGroup->all();
        $all_customOptions = $this->customOption->all();
        // フォルダー、ファイル名
        return view('managers.products.customs')->with([
            'all_customGroups' => $all_customGroups,
            'all_customOptions' => $all_customOptions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'extra_price' => 'nullable|array',
            'extra_price.*' => 'nullable|numeric',
        ]);

        // store_id は入れない（NULLで保存）
        $group = CustomGroup::create([
            'title' => $validated['title'],
            // 'store_id' => null, // 省略でOK
        ]);

        // name[] / extra_price[] を同じインデックスで対応させて保存
        foreach ($validated['name'] as $i => $name) {
            CustomOption::create([
                'custom_group_id' => $group->id,
                'name' => $name,
                'extra_price' => $validated['extra_price'][$i] ?? 0,
            ]);
        }
        return back();
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'extra_price' => 'nullable|array',
            'extra_price.*' => 'nullable|numeric',
            'option_ids' => 'nullable|array',        // 既存オプションのID配列
            'option_ids.*' => 'nullable|integer',
            'delete_ids' => 'nullable|array',        // 削除対象のID配列
            'delete_ids.*' => 'nullable|integer',
        ]);
    
        $group = CustomGroup::findOrFail($id);
        $group->update(['title' => $validated['title']]);
    
        // 🔹 削除対象があれば削除
        if (!empty($validated['delete_ids'])) {
            CustomOption::whereIn('id', $validated['delete_ids'])->delete();
        }
    
        // 🔹 既存オプションの更新または新規作成
        foreach ($validated['name'] as $i => $name) {
            $optionId = $validated['option_ids'][$i] ?? null;
    
            if ($optionId) {
                // 既存オプションを更新
                CustomOption::where('id', $optionId)->update([
                    'name' => $name,
                    'extra_price' => $validated['extra_price'][$i] ?? 0,
                ]);
            } else {
                // 新規オプションを作成
                CustomOption::create([
                    'custom_group_id' => $group->id,
                    'name' => $name,
                    'extra_price' => $validated['extra_price'][$i] ?? 0,
                ]);
            }
        }
    
        return back();
    }
    

    public function destroy($id)
    {
        $group = CustomGroup::findOrFail($id);
        $group->options()->delete(); // 関連するオプションも削除
        $group->delete();

        return back();
    }

}