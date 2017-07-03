<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>バリデーション</title>
  </head>
  <body>
    <?php
    //　ユーザーIDをチェック
    $userid = $_POST['userid'];
    
    // 正規表現 半角英数字で、文字の長さが6〜8文字の形式
    $pattern = '/^[a-zA-Z0-9]{6,8}+$/';
    
    // バリデーション実行
    if (preg_match($pattern, $userid)) {
      print($userid."：ユーザIDは正しい形式で入力されています。<br>");
    } else {
      print($userid."：ユーザIDは正しくない形式で入力されています。<br>");
    }
    
    //　年齢をチェック
    $age = $_POST['age'];
    
    // 正規表現 半角数字の形式
    $pattern = '/^[0-9]+$/';
    
    // バリデーション実行
    if (preg_match($pattern, $age)) {
      print($age."：正しい年齢の形式です。<br>");
    } else {
      print($age."：正しくない年齢の形式です。<br>");
    }
    
    // メールアドレスをチェック
    $email = $_POST['email'];

    // 正規表現
    $pattern = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/iD';

    // バリデーション実行
    if( preg_match($pattern, $email) ) {
      print($email."：正しいメールアドレスの形式です。<br>");
    }else{
      print($email."：正しくないメールアドレスの形式です。<br>");
    }

    // 電話番号をチェック
    $tel = $_POST['tel'];

    // 正規表現
    $pattern = '/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}/';

    // バリデーション実行
    if( preg_match($pattern, $tel) ) {
      print($tel."：正しい電話番号の形式です。<br>");
    }else{
      print($tel."：正しくない電話番号の形式です。<br>");
    }
    ?>
  </body>
</html>