<?php

namespace App\Http\Controllers;

use App\Models\CustomOption;
use App\Models\CustomGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomController extends Controller
{

    private $customGroup;
    private $customOption;

    public function __construct(CustomGroup $customGroup, CustomOption $customOption)
    {
        $this->customGroup = $customGroup;
        $this->customOption = $customOption;
    }

    public function index()
    {
        $userId = Auth::id();

        // ログインユーザーのデータだけ取得
        $all_customGroups = $this->customGroup
            ->where('user_id', $userId)
            ->with('customOptions') // 関連するオプションも一緒に取得
            ->get();

        return view('managers.products.customs')->with([
            'all_customGroups' => $all_customGroups,
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
            'user_id' => Auth::id(),
        ]);

        // name[] / extra_price[] を同じインデックスで対応させて保存
        foreach ($validated['name'] as $i => $name) {
            CustomOption::create([
                'custom_group_id' => $group->id,
                'name' => $name,
                'extra_price' => $validated['extra_price'][$i] ?? 0,
                'user_id' => Auth::id(),
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

        $group = CustomGroup::where('user_id', Auth::id())->findOrFail($id);
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

    public function options($id)
    {
        // CustomGroup モデルを想定
        $group = \App\Models\CustomGroup::with('options')->findOrFail($id);

        return response()->json([
            'options' => $group->options,
        ]);
    }
}
