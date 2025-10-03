<?php

return [
    // layouts.app
        // offcanvas menu
        'language'       => '言語',
        'japanese'       => '日本語',
        'english'        => '英語',
    'total'          => '合計',
    'paid'           => '支払い済み',
    'order_history'  => '注文履歴',
    'call_staff'    => 'スタッフを呼ぶ',
    'checkout'       => '退出',
    'payment'        => '会計',
    'cancel'        => 'キャンセル',
    'back'          => '戻る',
    'update'        => '更新',
    

    // welcome
    'welcome'        => ':storeへようこそ!',
    'thank_you_visiting'    => 'ご来店ありがとうございます。言語を選択し、ご来店人数を入力してください。',
    'apply'          => '適用',
    'number_of_guests' => '例) 4',
    'start_order'   => '注文を始める',
    'failed_change_language' => '言語の変更に失敗しました。もう一度お試しください。',
    'network_error' => 'ネットワークエラー。もう一度お試しください。',

    // index
    'menu_list' => 'メニュー一覧',
    'search_products' => '商品を検索...',
    'search_results' => '検索結果',

    //show
    'no_image' => '画像なし',
    'quantity' => '数量',
    'add_to_cart' => 'カートに追加',
    'add_to_cart_alert' => 'まず、商品の数量を1以上に設定してください。',
    'custom_alert' => 'このカスタムの合計数量は、商品の数量を超えることはできません。',
    'failed_add_alert' => 'カートへの追加に失敗しました。もう一度お試しください。',
    'allergen_labels'   =>  [
        'milk' => '乳製品',
        'egg' => '卵',
        'fish' => '魚',
        'shrimp' => 'エビ',
        'soy' => '大豆',
        'wheat' => '小麦',
        'sesame' => 'ごま',
        'cashew' => 'カシューナッツ',
        'walnut' => 'クルミ',
    ],

    // add-complete
    'add_success' => 'メニューが正常に追加されました！',
    'back_to_menu' => 'メニュー一覧に戻る',
    'view_cart' => 'カートを見る',

    // cart
    'cart' => 'カート',
    'confirm_delete' => '削除確認',
    'delete_confirmation' => 'カートから<strong>":item"</strong>を削除してもよろしいですか？',
    'complete_order' => '注文を完了する',
    'delete' => '削除',
    'cart_is_empty' => 'カートは空です。',
    'confirm_order_alert' => 'このボタンをクリックして注文を確定してください',

    // order-complete
    'order_success' => '注文が正常に完了しました！',
    'view_orders' => '注文を見る',

    //order-history
    'menu' => 'メニュー',
    'status' => 'ステータス',
    'option' => 'オプション',
    'price' => '値段',
    'qty' => '数量',
    'no_orders' => 'まだ注文はありません。',

    // call
    'call_server' => 'スタッフをお呼びしますか？',
    'call' => '呼ぶ',

    // call-complete
    'call_success' => 'スタッフをお呼びしました！',
    'priority' => '呼び出し順番',
    'acquisition_error' => '取得エラー',

    // payment-complete
    'payment_success' => '支払いが正常に完了しました！',

    // checkout
    'checkout_message' => '注文を終了しますか？',

    // checkout-complete
    'thank_you_paid' => 'ご来店ありがとうございました。',
    'thank_you_unpaid' => 'ご来店ありがとうございました。レジにお進みください。',
    'session_ended' => 'セッションが終了しました。ご不明点はスタッフまでお問い合わせください。',
];
