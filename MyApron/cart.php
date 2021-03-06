<?php
/* 最終課題のカート一覧ページ */
$host = 'localhost';
$username = '';   // MySQLのユーザ名
$password = '';     // MySQLのパスワード
$dbname = '';   // MySQLのDB名
$charset = 'utf8';   // データベースの文字コード
// MySQL用のDNS文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
$img_dir = './img/';  // 画像のディレクトリ
$sql_kind = '';   // SQL処理の種類
$err_msg  = [];   // エラーメッセージを格納する配列
$msg      = [];     //エラー以外のメッセージを格納する配列
$item_id  = '';
$total    = 0;

//セッション開始
session_start();

if(isset($_SESSION['user_id']) === TRUE){
  $user_id = $_SESSION['user_id'];
}else{
  header('location: login.php');//ログインしていなければ、ログイン画面へリダイレクト
}
  try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      // var_dump($_POST);
      if(isset($_POST['sql_kind']) === TRUE){
          $sql_kind= $_POST['sql_kind'];
      }
      
      if(isset($_POST['item_id']) === TRUE){
        $item_id = $_POST['item_id'];
      }
      
      if($sql_kind === 'change_amount'){
        if(isset($_POST['change_amount']) === TRUE){
          $amount = $_POST['change_amount'];
        }
        // var_dump($amount);
        $sql = 'UPDATE carts SET amount = ? WHERE item_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $amount,     PDO::PARAM_INT);
        $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
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
    
    // SQL文を実行する準備        
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $user_id,    PDO::PARAM_INT);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $cart_list = $stmt->fetchAll();
    //var_dump($cart_list);
 
    $total = 0;
    foreach($cart_list as $cart_item){
      $total += $cart_item['amount'] * $cart_item['price'];
    }
    //var_dump($total);
    
  } catch (PDOException $e) {
    // 例外をスロー
    //throw $e;
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
  <meta name="viewport" content="width=device-width, initial-scale=1.O">
  <link rel="stylesheet" href="./css/html5reset-1.6.1.css">
  <link rel="stylesheet" href="./css/MyApron_item.css">
  <link rel="stylesheet" href="./css/font-awesome.min.css">
  <title>My Apron | ショッピングカート</title>
</head>
<body>
  <header>
    <div class="container">
      <ul class="main-nav">
        <li>
          <a class="shop-title" href="login.php">My Apron</a>
        </li>
        <li>
          <a href="cart.php">
            <i class="fa fa-cart-arrow-down fa-3x" aria-hidden="true"></i>
          </a>
        </li>
        <li>
          <a class="logout-menu" href="logout.php">ログアウト</a>
        </li>
      </ul>
    </div>
  </header>
  
  <section>
    <div class="container">
      <h1>ショッピングカート</h1>
      <table class="cart_list-title">
        <thead>
          <tr>
            <th><span class="cart-list-item">ご注文商品</span></th>
            <th></th>
            <th><span class="cart-list-price">価格</span></th>
            <th><span class="cart-list-num">数量</span></th>
            <th></th>
          </tr>
          </thead>
          <tbody>
            <tr class="cart-list">
<?php foreach ($cart_list as $cart_item)  { ?>
              <td>
                <img class="cart-item-img" src="<?php print h($img_dir . $cart_item['img']); ?>">
              </td>
              <td class="item-list-name">
                <span class="cart-item-name"><?php print h($cart_item['name']); ?></span>
              </td>
              <td class="item-list-price">
                <span class="cart-item-price"><?php print h($cart_item['price']); ?>円</span>
              </td>
              <td>
                <form class="form_select_amount" method="post">
                  <input type="text" class="input_text_width text_align_right" name="change_amount" value="<?php print h($cart_item['amount']); ?>">個&nbsp;&nbsp;<input type="submit" value="変更">
                  <input type="hidden" name="item_id" value="<?php print h($cart_item['item_id']); ?>">
                  <input type="hidden" name="sql_kind" value="change_amount">
                </form>
              </td>
              <td>
              <form class="cart-item-del" method="post">
                <input type="submit" value="削除">
                <input type="hidden" name="item_id" value="<?php print h($cart_item['item_id']); ?>">
        	    　<input type="hidden" name="sql_kind" value="delete_cart_item">
              </form>
              </td>
            </tr>
<?php } ?>
          </tbody>
        </table>

        <div class="buy-sum-box">
          <span class="buy-sum-title">合計: </span>
          <span class="buy-sum-price"><?php print h($total); ?>円</span>
        </div>
        <div class="buy">
          <form action="finish.php" method="post">
            <input class="buy-btn" type="submit" value="購入する">
          </form>
        </div>
      </div>
  </section>
  
  <footer>
    <small>Copyright&copy;My Apron All Rights Reserved.</small>
  </footer>
</body>
</html>
