<?php

return [
    // Common
    'back'           => '戻る',
    'save'          => '保存',
    'delete'        => '削除',
    'cancel'        => 'キャンセル',
    'update'        => '更新',
    'add'            => '追加',

    // layouts.app
        // offcanvas menu
        'menu'           => 'メニュー',
        'custom'         => 'カスタム',
        'category'       => 'カテゴリー',
        'table'          => 'テーブル',
        'analytics'      => '分析',
        'logout'         => 'ログアウト',

    // home
    'tables'         => 'テーブル一覧',
    'order_list'     => '注文一覧',
    'admin_menu'     => 'メニュー管理',
    'store_info'    => '店舗情報',

    // stores.index
    'edit'            => '編集',
    'create_qr_code'    => 'QRコード作成',
    'not_store_info yet' => '店舗情報がまだ登録されていません。',
    'create_store_info' => '店舗情報を登録',

    // chats.chat
    'chat'            => 'チャット',
    'no_messages'   => 'まだメッセージはありません。',
    'enter_message' => 'メッセージを入力...',

    // stores.save
    'enable_payment' => '支払いを有効にする',
    'language'       => '言語',
    'english'        => '英語',
    'japanese'       => '日本語',
    'store_name'    => '店舗名',
    'address'       => '住所',
    'phone'         => '電話番号',
    'manager_name'  => 'マネージャー名',
    'open_hours'   => '営業時間',
    'email'         => 'メールアドレス',
    'currency'      => '通貨',
    'current_password' => '現在のパスワード',
    'new_password'  => '新しいパスワード',
    'confirm_password' => 'パスワード確認',

    // stores.qr
    'table_number' => 'テーブル番号',
    'start'          => '開始',
    'end'            => '終了',
    'no.0_table'   => 'テイクアウト注文を受け付ける場合は、No.0から始めてください',
    'generate_qr_code' => 'QRコード生成',
    'print'          => '印刷',

    // products.products
    'menu_list'     => 'メニュー一覧',
    'add_menu'        => 'メニュー追加',
    'search_products'        => '商品を検索...',
    'search_results' => '検索結果',
    'new_category'   => '新しいカテゴリー',

    // products.partials.products
    'no_results'   => '結果が見つかりません',
    'no_products' => 'まだ商品が追加されていません。',
    'no_image'     => '画像なし',

    // order-lists.order-lists
    'show_completed' => '完了済みを表示',
    'table_no'       => 'テーブル番号',
    'time'           => '経過時間',
    'item'           => '商品',
    'option'         => 'オプション',
    'quantity'      => '数量',
    'order_type'   => '注文タイプ',
    'progress'      => '進行状況',
    'update_failed'  => '更新に失敗しました',
    'connection_error' => '接続エラー',

    // products.categories
    'category_list' => 'カテゴリー一覧',
    'add_category'   => 'ここにカテゴリーを追加...',
    'edit_category'  => 'カテゴリー編集',

    // tables.tables
    'table_list'   => 'テーブル一覧',
    'no_tables'   => 'テーブルが見つかりません。',

    // tables.show
    'back_to_tables' => 'テーブル一覧に戻る',
    'options'       => 'オプション',
    'price'         => '値段',
    'qty'           => '数量',
    'status'        => 'ステータス',
    'total'         => '合計',
    'unpaid'        => '未会計',
    'paid_via'      => '会計済',
    'payment'         => '会計',
    'checkout'      => 'チェックアウト',
    'select_payment'  => '支払い方法を選択',
    'cash'          => '現金',
    'credit_card'   => 'クレジットカード',
    'qr_code'      => 'QRコード',
    'other'         => 'その他',
    'confirm'        => '確認',
    'no_orders'     => 'まだ注文はありません。',

    // stores.analytics
    'store_analytics' => '店舗分析',
    'sales'          => '売上',
    'products'      => '商品',
    'sales_trend'      => '売上傾向',
    'daily'          => '日次',
    'weekly'         => '週次',
    'monthly'        => '月次',
    'reset'         => 'リセット',
    'apply'         => '適用',
    'date'            => '日付',
    'day'             => '曜日',
    'guests'          => '来店人数',
    'ave_spend'      => '客単価',
    'payment_method'  => '支払い方法',
    'top_5_products'   => '売上上位5商品',
    'term_alert'    =>      '開始日と終了日の両方を選択してください。',

    // partials.analytics_order_details
    'date_time'    => '日付/時間',
    'stay_duration' => '滞在時間',

    // products.add-product
    'image'          => '画像',
    'upload_menu_image'  => 'メニュー画像をアップロード',
    'menu_name'     => 'メニュー名',
    'description'   => '説明',
    'tag'        => 'タグ',
    'upload_tag_image' => 'タグ画像をアップロード',
    'allergens'      => 'アレルゲン',
    'custom_options'  => 'カスタムオプション',
    'required'    => '必須',
    'see_all_customs' => 'すべてのカスタムを見る',
    'add_option'    => 'オプションを追加',
    'select_custom'  => 'カスタムを選択',
    'add_category_fisrst' => '最初にカテゴリーを追加してください',
    'select_category' => 'カテゴリーを選択',

    // products.custom
    'custom_name'   => 'カスタム名',
    'option_name' => 'オプション名',
    'no_options' => 'まだオプションが追加されていません。',
    'edit_custom'   => 'カスタムを編集',

    // products.show
    'no_allergens' => 'アレルゲンなし',
    'delete_product' => '本当に<strong>":product"</strong>を削除しますか？',

    // products.edit
    'added_alert' => 'すべてのカスタムはすでに追加されています。',
];
