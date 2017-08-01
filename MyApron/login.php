<?php
/* 最終課題のログインページ */

// データベースの接続情報
//$dsn の代わりにDSNが使えるようになりました。
define('DB_USER',   'risayamasaki');    // MySQLのユーザ名
define('DB_PASSWD', '');    // MySQLのパスワード
define('DSN', 'mysql:dbname=camp;host=localhost;charset=utf8');

$err_msg = [];  // エラーメッセージ用の配列
$msg      = [];     //エラー以外のメッセージを格納する配列
$user_name = ''; //初期化
$password = '';
//var_dump($_POST);

//セッション開始
session_start();

// セッション変数からログイン済みか確認
if (isset($_SESSION['user_id'])) {
  // ログイン済みの場合、ホームページへリダイレクト
  header('Location: itemlist.php');
  exit;
}

// POST値取得
if (isset($_POST['user_name']) === TRUE){
  //こっちは置換処理なのでpreg_replace
  $user_name = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['user_name']);
}
if($user_name === ''){ 
  $err_msg[] = 'ユーザー名を入力してください。';
}else if(preg_match('/^[a-z\d_]{6,20}$/i', $user_name) !== 1){ 
  $err_msg[] = "ユーザー名は半角英数字6文字以上でご入力ください。";
}
  
if (isset($_POST['password']) === TRUE) {
  $password = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['password']);
}    
//パスワードエラーチェック
if($password === ''){ 
  $err_msg[] = 'パスワードを入力してください。';
}else if(preg_match('/^[a-z\d_]{6,20}$/i', $password) !== 1){
  $err_msg[] = "パスワードは半角英数字6文字以上でご入力ください。";
}
//var_dump($err_msg);

if(count($err_msg) ===0) {

  try {
    // データベースに接続
    //ここの$dsn, $username, $passwordが定数DSN, DB_USER, DB_PASSWDで置き換え
    $dbh = new PDO(DSN, DB_USER, DB_PASSWD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $sql = 'SELECT 
              id
              FROM  users
              WHERE user_name = ? AND password = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $user_name, PDO::PARAM_INT);
    $stmt->bindValue(2, $password,  PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    //var_dump($rows);
    // if(count($rows) >=1){ //1件以上取得できたら
      
    // }
    if(isset($rows[0]['id']) === true){ //idが取得できていたら
      //セッションにuser_idを保存する処理
      $_SESSION['user_id'] = $rows[0]['id'];
      header('Location: itemlist.php');
      exit;
    }else{
      //user_idを取得できなかった場合（失敗メッセージ）
      $err_msg[] = 'ログインできませんでした。';
    }
    
  }catch (PDOException $e) {
    echo 'データベース処理でエラーが発生しました。理由：'.$e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
  <!--最小限のビューポート設定-->
  <!--<meta name="viewport" content="width=device-width">-->
  <title>My Apron：ログインページ</title>
  <link rel="stylesheet" href="MyApron.css">
</head>
<body>
  <header>
    <div class="container">
      <a href="login.php">
        <img class="logo" src="./img/logo.png" alt="MyApron">
      </a>
    </div>
  </header>
  <main>
  <div class="contents">
    <img class="topvie" src="./img/top1.jpg" alt="MyApronTop">
  </div>
    <div class="register">
      <form method="post" action="login.php">
        <div>ユーザー名：<input type="text" name="user_name" placeholder="ユーザー名"></div>
        <div>パスワード：<input type="password" name="password" placeholder="パスワード"></div>
        <div><input type="submit" value="ログイン"></div>
      </form>
      </div>
    <div class="register_text">
       <a href="register.php">ユーザーの新規作成</a>
    </div>
    </main>
    <footer>
    <!--<div class="container">-->
      <div class="footer-navi">
      <small>Copyright&copy;My Apron All Rights Reserved.</small>
    </div>
    <!--</div>-->
  </footer>
</body>
</html>