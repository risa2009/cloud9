<?php
   $host     = 'localhost';
   $username = 'risayamasaki';   // MySQLのユーザ名
   $password = '';       // MySQLのパスワード
   $dbname   = 'camp';   // MySQLのDB名
   $charset  = 'utf8';   // データベースの文字コード

    // MySQL用のDSN文字列
    $dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

    $date = [];
    $err_msg = [];  // エラーメッセージ用の配列

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $user_name = '';
    $user_comment = '';

    //名前が入力されているかどうか　と　0文字だったらエラーを追加する処理
    if(isset($_POST['user_name']) !== TRUE || mb_strlen($_POST['user_name']) === 0){
        $err_msg[] = "名前を入力してください。";
    }else if(mb_strlen ($_POST['user_name'], 'utf-8') > 20){
        $err_msg[] = "名前は20文字以内で入力してください。";
    }else{
        //入力値が正しかった場合はPOSTから変数へ詰め替え
        $user_name = trim($_POST['user_name']);
    }
    
    if (isset($_POST['user_comment']) !== TRUE || mb_strlen($_POST['user_comment']) === 0) {
        $err_msg[] = "ひとことを入力してください。";
    }else if (mb_strlen($_POST['user_comment'], 'utf-8') > 100) {
        $err_msg[] = "コメントは100文字以内で入力してください。";
    }else{
        //入力値が正しかった場合はPOSTから変数へ詰め替え
        $user_comment = trim($_POST['user_comment']);
    }
}
      
    try {
      // データベースに接続
      $dbh = new PDO($dsn, $username, $password);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

      if($_SERVER['REQUEST_METHOD'] === 'POST'){
          $sql = 'INSERT INTO post (user_name, user_comment, create_datetime)
          VALUES (?, ?, ?)';
        
        // 現在日時を取得
      $now_date = date('Y-m-d H:i:s');
           
          // SQL文を実行する準備
          $stmt = $dbh->prepare($sql);
          
          //SQLにプレスフォルダの値をバイント
          $stmt->bindValue(1, $user_name, PDO::PARAM_STR);
          $stmt->bindValue(2, $user_comment, PDO::PARAM_STR);
          $stmt->bindValue(3, $now_date, PDO::PARAM_STR);
          
          $stmt->execute();
      }
      //SELECT文の実行する準備(prepare,  execute)
      $sql = 'SELECT user_name, user_comment, create_datetime FROM post';
      $stmt = $dbh->prepare($sql);
      
      // SQLを実行
      $stmt->execute();
      
      // レコードの取得
      $rows = $stmt->fetchAll();
      var_dump($rows);

    } catch (PDOException $e) {
      echo '接続できませんでした。理由：'.$e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
 <meta charset="UTF-8">
 <title>ひとこと掲示板</title>
</head>
<body>
  <h1>ひとこと掲示板</h1>
<?php
foreach ($err_msg as $value) { 
?>
<ul>
    <li>
        <?php print $value; ?>
    </li>
</ul>
<?php 
} ?>
  <form action="bbs.php" method="post">
      <label>名前: </label><input type="text" name="user_name">
      <label>ひとこと: </label><input type="text" name="user_comment" size="60">
      <input type="submit" name="submit" value="送信">
  </form>
<?php
foreach ($rows as $read) { 
?>
  <ul>
    <li>
<?php print $read['user_name']. $read['user_comment']. $read['create_datetime']; 
?>
    </li>
  </ul>
<?php 
} ?>
</body>
</html>