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
    //こちらが問題ですね！
    $sql = 'SELECT
            user_name
            FROM users
            WHERE user_name = ?';
    // 変数が入る位置がプレースホルダとなるので、
    // このようになります。
    
     // SQL文を実行する準備
     $stmt = $dbh->prepare($sql);
     //SQLにプレスフォルダの値をバイント
     $stmt->bindValue(1, $user_name, PDO::PARAM_STR);
     // SQLを実行
     $stmt->execute();
     // レコードの取得
     $rows = $stmt->fetchAll();
     
     // select文の実行結果が１行以上あればエラーメッセージを表示
     if ($user_name['user_name'] === $user_name) {
       $err_msg[] = 'ユーザ名がすでに登録されています。';
     }
     
     // count($err_msg)をチェック
     
     // エラーがなければユーザーをinsert
   try {
     $sql = 'INSERT INTO users (user_name, password, created_at)
              　　VALUES (?, ?, ?)';
     // SQL文を実行する準備
     $stmt = $dbh->prepare($sql);
     // SQL文のプレースホルダに値をバインド
     $stmt->bindValue(1, $user_name,    PDO::PARAM_STR);
     $stmt->bindValue(2, $password,     PDO::PARAM_STR);
     $stmt->bindValue(3, $created_at,   PDO::PARAM_STR);
     
     // SQLを実行
     $stmt->execute();
     
     // レコードの取得
     $rows = $stmt->fetchAll();
     
     // 1行ずつ結果を配列で取得します
     $i = 0;
     foreach ($rows as $row) {
       $data[$i]['user_name']    = htmlspecialchars($row['user_name'],   ENT_QUOTES, 'UTF-8');
       $data[$i]['password']     = htmlspecialchars($row['password'],    ENT_QUOTES, 'UTF-8');
       $data[$i]['created_at']   = htmlspecialchars($row['created_at'],  ENT_QUOTES, 'UTF-8');
       $i++;
     }
     
     // 表示メッセージの設定
      $result_msg = 'アカウント作成が完了しました。<br>';
     } catch (PDOException $e) {
      // 例外をスロー
       throw $e;
     }
   } catch (PDOException $e) {
     $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。'.$e->getMessage();
  }
  }
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>My Apron：無料会員登録</title>
  </head>
  <body>
  </body>
</html>