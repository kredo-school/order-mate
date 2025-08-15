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

}