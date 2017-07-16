<?php
/* 最終課題の会員登録ページ */

// データベースの接続情報
//$dsn の代わりにDSNが使えるようになりました。
define('DB_USER',   'risayamasaki');    // MySQLのユーザ名
define('DB_PASSWD', '');    // MySQLのパスワード
define('DSN', 'mysql:dbname=camp;host=localhost;charset=utf8');

 $err_msg = [];  // エラーメッセージ用の配列

// ログインボタンが押された場合

if (isset($_POST['login']) === TRUE){
 $login = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['user_name']);  //全角と半角の空白を取り除く。受け取り
    }
// エラーチェック
   if()
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>My Apron：カート</title>
  <link rel="stylesheet" href="MyApron.css">
</head>
<body>
  <header>
  <div class="header-box">
    <a href="#">
    <img class="logo" src="./img/logo.png" alt="MyApron">
    </a>
  </div>
  </header>
  <div class="cart_list">
  <div class="cart">
  <table>
   <tr><th>商品名</th><th>単価</th><th>数量</th><th>小計</th></tr>
   <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
   </tr>
   <tr><td colspan='2'> </td><td><strong>合計</strong></td><td>円</td></tr>
  </table>
  </div>
  </div>
</body>
</html>