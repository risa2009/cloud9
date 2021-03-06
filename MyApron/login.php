<?php
/* ログインページ */

// データベースの接続情報 ($dsn の代わりにDSNを利用)
define('DB_USER',   '');    // MySQLのユーザ名
define('DB_PASSWD', '');                // MySQLのパスワード
define('DSN', 'mysql:dbname=camp;host=localhost;charset=utf8');

$err_msg = [];      // エラーメッセージ用の配列
$msg      = [];     //エラー以外のメッセージを格納する配列
$user_name = '';    //初期化
$password = '';
//var_dump($_POST);

// セッション開始
session_start();

// セッション変数からログイン済みか確認
if (isset($_SESSION['user_id'])) {
  // ログイン済みの場合、ホームページへリダイレクト
  header('Location: itemlist.php');
  exit;
}

// POST値取得
if (isset($_POST['user_name']) === TRUE){
  // 置換処理なのでpreg_replace
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
// パスワードエラーチェック
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
  <meta name="viewport" content="width=device-width, initial-scale=1.O">
  <link rel="stylesheet" href="./css/MyApron_form.css">
  <link rel="stylesheet" href="./css/font-awesome.min.css">
  <title>My Apron | ログインページ</title>
</head>
<body>
 <header>
    <div class="container">
      <div class="logo">
        <a href="login.php">
        <img src="./img/logo.png" alt="MyApron">
        </a>
      </div>
     </div>
  </header>
  <section>
    <div class="container">
      <form method="post" action="login.php">
        <ul class='login_form'>
          <li class='form-Tit'>ユーザー名：</li>
          <li><input class='form-Txt' type="text" name="user_name" placeholder="ユーザー名"></li>
          <li class='form-Tit'>パスワード：</li>
          <li><input class='form-Txt' type="password" name="password" placeholder="パスワード"></li>
          <li class="register_text"><a href="register.php">ユーザーの新規作成</a></li>
          <li><input class='login-btn' type="submit" value="ログイン"></li>
        </ul>
      </form>
    </div>
  </section>
  <footer>
    <small>Copyright&copy;My Apron All Rights Reserved.</small>
  </footer>
</body>
</html>
