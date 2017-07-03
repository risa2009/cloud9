<?php
$filename = './review.txt';
$User_name = [];
$comment = [];
$date = date("-Y-m-d H:i:s");
$err_msg = [];  // エラーメッセージ用の配列

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    //名前が入力されているかどうか　と　0文字だったらエラーを追加する処理
    if(isset($_POST['User_name']) !== TRUE || mb_strlen($_POST['User_name']) === 0){
        $err_msg[] = "名前を入力してください。";
    }else if(mb_strlen ($_POST['User_name'], 'utf-8') > 20){
        $err_msg[] = "名前は20文字以内で入力してください。";
    }else{
        //入力値が正しかった場合はPOSTから変数へ詰め替え
        $User_name = trim($_POST['User_name']);
    }
    
    if (isset($_POST['comment']) !== TRUE || mb_strlen($_POST['comment']) === 0) {
        $err_msg[] = "ひとことを入力してください。";
    }else if (mb_strlen($_POST['comment'], 'utf-8') > 100) {
        $err_msg[] = "コメントは100文字以内で入力してください。";
    }else{
        //入力値が正しかった場合はPOSTから変数へ詰め替え
        $comment = trim($_POST['comment']);
    }
      
      //エラーチェックが終わったら　$err_msg　を　カウントします。
    if(count($err_msg) === 0){
      //ファイルに書き込み処理
       if (($fp = fopen($filename, 'a')) !== FALSE) {
        if (fwrite($fp, $User_name . "\t" . $comment . "\t" . $date . "\t\n") === FALSE) {
          print 'ファイル書き込み失敗: ' . $filename;
        }
       }
      
      fclose($fp);
    } 
}

$data = [];

if (is_readable($filename) === TRUE) {
 if(($fp = fopen($filename, 'r')) !== FALSE) {
    while (($tmp = fgets($fp)) !== FALSE) {
        $data[] = htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8');
    }
    fclose($fp);
 }
} else {
    $data[] = 'ファイルがありません';
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
  <form method="post">
      <label>名前: </label><input type="text" name="User_name">
      <label>ひとこと: </label><input type="text" name="comment" size="60">
      <input type="submit" name="submit" value="送信">
  </form>
<?php
foreach ($data as $read) { 
?>
  <ul>
    <li>
<?php print $read; ?>
    </li>
  </ul>
<?php 
} ?>
</body>
</html>