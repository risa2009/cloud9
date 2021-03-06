<?php
/* 最終課題の商品一覧ページ */
$host     = 'localhost';
$username = '';   // MySQLのユーザ名
$password = '';       // MySQLのパスワード
$dbname   = '';   // MySQLのDB名
$charset  = 'utf8';   // データベースの文字コード
 
// MySQL用のDNS文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$img_dir    = './img/';  // 画像のディレクトリ

$err_msg    = [];     // エラーメッセージを格納する配列
$msg        = [];     //エラー以外のメッセージを格納する配列

$item_id    = '';

//セッション開始
session_start();

if(isset($_SESSION['user_id']) === TRUE){
  $user_id = $_SESSION['user_id'];
}else{
  header('location: login.php');//ログインしていなければ、ログイン画面へリダイレクト
}

if (isset($_POST['item_id']) === TRUE) {
    $item_id = $_POST['item_id'];
}
// 現在日時を取得
$now_date = date('Y-m-d H:i:s');
  
try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    //select文でカート内のデータを取得
    $sql = 'SELECT
                 carts.user_id,
                 carts.item_id,
                 carts.amount,
                 items.name
            FROM carts JOIN items
            ON carts.item_id = items.item_id
            WHERE carts.item_id = ?
            AND user_id = ?';
   
    $stmt = $dbh->prepare($sql);
      
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $item_id,    PDO::PARAM_INT);
    $stmt->bindValue(2, $user_id,    PDO::PARAM_INT);
        
    // SQLを実行
    $stmt->execute();
    
    // レコードの取得
    $cart_list = $stmt->fetchAll();
    //var_dump($cart_list);
    
    //カート内に該当のレコードがあるかどうかをチェック
    if(count($cart_list) >= 1){ //レコードが一つ以上取得できれば
    
      $sql = 'UPDATE carts
              SET amount = ?
              WHERE item_id = ?';
        
      //レコード一つだけなので$cart_listの0番目に取得されている。
      $amount = $cart_list[0]['amount'] + 1;
      
      $stmt = $dbh->prepare($sql);
      $stmt->bindValue(1, $amount,     PDO::PARAM_INT);
      $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
      $stmt->execute();
      
     $msg[] = 'カートに商品を追加しました。';
      
    }else{
      $amount = 1; //まだカートに一つも入っていない状態
      //$stmt->bindValue(3, 1, PDO::PARAM_INT);としてもOK

      $sql =  'INSERT INTO carts (user_id, item_id, amount, create_datetime) 
              VALUES (?, ?, ?, ?)';
      
      $stmt = $dbh->prepare($sql);
      $stmt->bindValue(1, $user_id,    PDO::PARAM_INT);
      $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
      $stmt->bindValue(3, $amount,     PDO::PARAM_INT);
      $stmt->bindValue(4, $now_date,   PDO::PARAM_STR);
      
      $stmt->execute();
    }
      
  }

  // 公開商品のみ表示
  $sql = 'SELECT 
              items.item_id,
              items.name,
              items.price,
              items.img,
              items.status,
              items.stock
          FROM items
          WHERE status = 1';
              
  // SQL文を実行する準備
  $stmt = $dbh->prepare($sql);
  
  // SQLを実行
  $stmt->execute();
  
  // レコードの取得
  $item_list = $stmt->fetchAll();

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
  <meta name="viewport" content="width=device-width, initial-scale=1.O">
  <link rel="stylesheet" href="./css/html5reset-1.6.1.css">
  <link rel="stylesheet" href="./css/MyApron_item.css">
  <link rel="stylesheet" href="./css/font-awesome.min.css">
  <title>My Apron | 商品一覧</title>
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
  <section class="item_list">
    <div class="container">
      <h1>今週のメニュー</h1>
<?php foreach ($msg as $value) { ?>
       <p><?php print h($value); ?></p>
<?php } ?>
      
      <table>
        <tr>
          <th>メニュー</th>
          <th>商品名</th>
          <th>価格</th>
          <th>購入ボタン</th>
        </tr>
<?php foreach ($item_list as $value)  { ?>
        <tr class="item">
          <form method="post">
            <td>
              <img class="item-list-img" src="<?php print h($img_dir . $value['img']); ?>">
            </td>
            <td class="item-list-name">
              <span><?php print h($value['name']); ?></span>
            </td>
            <td class="item-list-price">
              <span><?php print h($value['price']); ?>円</span>
            </td>
            <input type="hidden" name="sql_kind" value="add_product_to_cart">
<?php if ($value['stock'] > 0) { ?>
            <input type="hidden" name="item_id" value="<?php print h($value['item_id']); ?>">
<?php 
} else {
?>
            <span>売り切れ</span>
<?php } ?>
            <td class="cart-btn">
              <input type="submit" value="カートに入れる">
            </td>
          </form>
        </tr>
<?php } ?>
      </table>
      
<?php foreach ($err_msg as $value) { ?>
        <p><?php print h($value); ?></p>
<?php } ?>
    </div>
  </section>
  
  <footer>
    <small>Copyright&copy;My Apron All Rights Reserved.</small>
  </footer>
</body>
</html>
