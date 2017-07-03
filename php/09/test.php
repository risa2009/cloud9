<?php

// 送信ボタンがクリックされた場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>課題</title>
</head>
<body>
  <h1>課題</h1>
  <form method='post'>
      <p>お名前：<input id="my_name" type="text" name="my_name" value=""></p>
      <p>性別： <input type="radio" name="gender" value="man">男
      <input type="radio" name="gender" value="woman">女</p>
      <p><input type="checkbox" name="mail" volue="OK">お知らせメールを受け取る</p>
      <input type="submit" name="submit" value="送信">
  </form>
</body>
</html>