<?php
// 設定ファイル読み込み
require_once './conf/setting.php';
// 関数ファイル読み込み
require_once './model/model.php';

 $date = [];
 $errors = [];  // エラーメッセージ

$request_method = get_request_method();

if ($request_method === 'POST') {
  
  $user_name = get_post_data('user_name');
  
  // 名前が正しく入力されているかチェック
  $result = check_user_name($user_name);
  
  if ($result !== TRUE) {
    $errors[] = $result;
  }
  
  $user_comment = get_post_data('user_comment');
  
  // ひとことが正しく入力されているかチェック
  $result = check_user_comment($user_comment);
  
  if ($result !== TRUE) {
    $errors[] = $result;
  }
}

$link = get_db_connect();

// エラーがなければ保存
if ($request_method ==='POST' && count($errors) ===0) {
  
  // 現在日時を取得
  $now_date = date('Y-m-d H:i:s');
  
  try {
    
    insert_post($link, $user_name, $user_comment, $now_date);
    
    // リロード対策でリダイレクト
    header('Location: http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
    
  } catch (PDOException $e) {
    $errors[] = 'レコード追加失敗';
  }
}

// 掲示板の書き込み一覧を取得する
$data = get_post_list($link);

// 特殊文字をHTMLエンティティに変換する
$data = entity_assoc_array($data);

// テンプレートファイル読み込み
include_once './view/view.php';