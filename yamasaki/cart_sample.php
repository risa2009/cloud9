<?php
/* 最終課題のカート一覧ページ */
$host = 'localhost';
$username = 'risayamasaki';   // MySQLのユーザ名
$password = '';     // MySQLのパスワード
$dbname = 'camp';   // MySQLのDB名
$charset = 'utf8';   // データベースの文字コード

// MySQL用のDNS文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
$img_dir = './img/';  // 画像のディレクトリ
$sql_kind = '';   // SQL処理の種類
$err_msg  = [];   // エラーメッセージを格納する配列
$msg      = [];     //エラー以外のメッセージを格納する配列
$item_id  = '';
$total    = 0;

session_start();
//ログインチェックの処理（ログイン実装後にコメントを外す）
if(isset($_SESSION['user_id']) === TRUE){
  $user_id = $_SESSION['user_id'];
}else{
  header('location: login_sample.php');//ログインしていなければ、ログイン画面へリダイレクト
}
  
try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  
  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['sql_kind']) === TRUE){
        $sql_kind= $_POST['sql_kind'];
    }
    
    if(isset($_POST['item_id']) === TRUE){
      $item_id = $_POST['item_id'];
    }
    
    if($sql_kind === 'change_amount'){ //購入数変更の処理
      //変更する購入数の受け取り
      if(isset($_POST['change_amount']) === TRUE){
        $amount = $_POST['change_amount'];
      }
      $sql = 'UPDATE carts SET amount = ? WHERE item_id = ? AND user_id = ?';
      $stmt = $dbh->prepare($sql);
      $stmt->bindValue(1, $amount,     PDO::PARAM_INT);
      $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
      $stmt->bindValue(3, $user_id,    PDO::PARAM_INT);
      $stmt->execute();
      $msg[] = '購入数を変更しました。';
      
    }else if($sql_kind === 'delete_cart_item'){
      //カートからの削除処理
      $sql = 'DELETE FROM carts WHERE item_id = ? AND user_id = ?';
      $stmt = $dbh->prepare($sql);
      $stmt->bindValue(1, $item_id,   PDO::PARAM_INT);
      $stmt->bindValue(2, $user_id,    PDO::PARAM_INT);
      $stmt->execute();
      $msg[] = '削除しました。';
    }
  }
  
  //ログインユーザーのカート一覧を取得
  $sql = 'SELECT
               carts.user_id,
               carts.item_id,
               carts.amount,
               items.name,
               items.price,
               items.img
          FROM carts JOIN items
          ON carts.item_id = items.item_id
          WHERE carts.user_id = ?';
  
  $stmt = $dbh->prepare($sql);
  $stmt->bindValue(1, $user_id,    PDO::PARAM_INT);
  $stmt->execute();
  $cart_list = $stmt->fetchAll();
  
  //合計金額の計算処理
  $total = 0;
  foreach($cart_list as $cart_item){
    //購入数 * 単価を合計していく
    $total += $cart_item['amount'] * $cart_item['price'];
  }

} catch (PDOException $e) {
  echo 'データベース処理でエラーが発生しました。理由：'.$e->getMessage();
}

function h($str){
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>My Apron：ショッピングカート</title>
  <link rel="stylesheet" href="../MyApron.1.css">
  <style>
    table{
      border-collapse: collapse;
      text-align: center;
    }
    th, td{
      border: solid 1px #000000;
    }
    .buy-sum-box{
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    <div class="header-box">
      <a href="https://risayamasaki-risayamasaki.c9users.io/MyApron/itemlist_sample.php">
        <img class="logo" src="./img/logo.png" alt="MyApron">
      </a>
      <a class="nemu" href="logout_sample.php">ログアウト</a>
      <a href="https://risayamasaki-risayamasaki.c9users.io/MyApron/cart_sample.php" class="cart">
        <img class="logo" src="./img/cart.png" alt="MyApron">
      </a>
    </div>
  </header>
  <div class="cart_list">
    <h1>ショッピングカート</h1>
<!-- ここに個別のアイテムを記述 -->
    <div class="cart-list-title">
      <span class="cart-list-price">商品</span>
      <span class="cart-list-num">数量</span>
    </div>
    <table class="cart-list">
      <tr>
        <th>画像</th>
        <th>商品名</th>
        <th>金額</th>
        <th>購入数</th>
        <th>削除</th>
      </tr>
<?php foreach ($cart_list as $cart_item)  { ?>
      <tr class="cart-item">
        <td>
          <span class="item_img_size"><img src="<?php print h($img_dir . $cart_item['img']); ?>"></span>
        </td>
        <td>
          <span><?php print h($cart_item['name']); ?></span>
        </td>
        <td>
          <span class="cart-item-price"><?php print h($cart_item['price']); ?>円</span>
        </td>
        <td>
          <form method="post">
            <input type="text"  class="input_text_width text_align_right" name="change_amount" value="<?php print h($cart_item['amount']); ?>">個&nbsp;&nbsp;<input type="submit" value="変更">
            <input type="hidden" name="item_id" value="<?php print h($cart_item['item_id']); ?>">
            <input type="hidden" name="sql_kind" value="change_amount">
          </form>
        </td>
        <td>
          <form method="post">
            <input type="submit" value="削除">
            <input type="hidden" name="item_id" value="<?php print h($cart_item['item_id']); ?>">
    	    　<input type="hidden" name="sql_kind" value="delete_cart_item">
          </form>
        </td>
      </tr>
<?php } ?>
    </table>
    <div class="buy-sum-box">
      <span class="buy-sum-title">合計:</span>
      <span class="buy-sum-price"><?php print h($total); ?>円</span>
    </div>
    <div>
      <form action="finish_sample.php" method="post">
        <input class="buy-btn" type="submit" value="購入する">
      </form>
    </div>
  </div>
</body>
</html>