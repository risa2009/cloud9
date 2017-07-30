<?php
/* 最終課題の会員登録ページ */

// データベースの接続情報
//$dsn の代わりにDSNが使えるようになりました。
define('DB_USER',   'risayamasaki');    // MySQLのユーザ名
define('DB_PASSWD', '');    // MySQLのパスワード
define('DSN', 'mysql:dbname=camp;host=localhost;charset=utf8');  
   
$date = [];
$err_msg = [];  // エラーメッセージ用の配列
$result_msg = '';     // 実行結果のメッセージ
    
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $user_name = ''; //初期化
  $password = '';
          
  if (isset($_POST['user_name']) === TRUE) { //issetでのチェック
    $user_name = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['user_name']);  //全角と半角の空白を取り除く。受け取り
  }
  //ここからエラーチェック
  if($user_name === ''){ //未入力チェック
    $err_msg[] = 'ユーザー名を入力してください。';
    
  }else if(preg_match('/^[a-z\d_]{6,20}$/i', '', $_POST['user_name'])){ //正規表現チェック
    $err_msg[] = "ユーザー名は半角英数字6文字以上でご入力ください。";
  }
    
  if (isset($_POST['password']) === TRUE) { //issetでのチェック
    $password = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['password']); //全角と半角の空白を取り除く。受け取り
  }
  //ここからエラーチェック
  if($password === ''){ //未入力チェック
    $err_msg[] = 'ユーザー名を入力してください。';
  }else if(preg_match('/^[a-z\d_]{6,20}$/i', '', $_POST['password'])){ //正規表現チェック
    $err_msg[] = "ユーザー名は半角英数字6文字以上でご入力ください。";
  }
}
  
// DB接続前にcount($err_msg)をチェック
if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // データベースに接続
    //ここの$dsn, $username, $passwordがそれぞれ定数DSN, DB_USER, DB_PASSWDで置き換え
    $dbh = new PDO(DSN, DB_USER, DB_PASSWD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
     
    // 現在日時を取得
    $now_date = date('Y-m-d H:i:s');
     
    // select文で重複ユーザーをチェック
    $sql = 'SELECT
          user_name
          FROM users
          WHERE user_name = ?';
    
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    //SQLにプレスフォルダの値をバイント
    $stmt->bindValue(1, $user_name, PDO::PARAM_INT);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $user_list = $stmt->fetchAll();
    if (count($user_list) >= 1) {
      $err_msg[] = 'ユーザ名がすでに登録されています。';
    }
    if(count($err_msg) === 0) {
      // エラーがなければユーザーをinsert
      try {
        $sql = 'INSERT INTO users (user_name, password, created_at)
                VALUES (?, ?, ?)';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $user_name,    PDO::PARAM_STR);
        $stmt->bindValue(2, $password,     PDO::PARAM_STR);
        $stmt->bindValue(3, $now_date,   PDO::PARAM_STR);
        
        // SQLを実行
        $stmt->execute();
        
        // 表示メッセージの設定
        $result_msg = 'アカウント作成が完了しました。<br>';
      } catch (PDOException $e) {
      // 例外をスロー
       throw $e;
      }
    }
  } catch (PDOException $e) {
    $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。'.$e->getMessage();
  }
}
//var_dump($err_msg);//仮
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
  <title>My Apron：無料会員登録</title>
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
    <div class="register">
      <p><?php print($result_msg); ?></p>
      <form method="post" action="register.php">
        <div>ユーザー名：<input type="text" name="user_name" placeholder="ユーザー名"></div>
        <div>パスワード：<input type="password" name="password" placeholder="パスワード"></div>
        <div class="register-btn"><input type="submit" value="新規登録"></div>
      </form>
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