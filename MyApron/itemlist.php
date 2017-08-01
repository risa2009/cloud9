<?php
/* 最終課題の商品一覧ページ */
$host     = 'localhost';
$username = 'risayamasaki';   // MySQLのユーザ名
$password = '';       // MySQLのパスワード
$dbname   = 'camp';   // MySQLのDB名
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
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
  <title>My Apron：商品一覧</title>
  <link rel="stylesheet" href="MyApron.css">
</head>
<body>
  <header>
    <div class="container">
      <a href="itemlist.php">
      <img  src="./img/logo.png" alt="MyApron">
      </a>
    <div class="cart_logo">
      <a href="cart.php"><img src="./img/cart.png" alt="MyApron"></a>
    </div>
    <div class="nemu">
    <a href="logout.php">ログアウト</a>
    </div>
    </div>
  </header>
  <main>
  <h1>今週のメニュー</h1>
<?php foreach ($msg as $value) { ?>
     <p><?php print h($value); ?></p>
<?php } ?>
  <div class="item_list">
<?php foreach ($item_list as $value)  { ?>
  <div class="item">
      <form method="post">
        <sapn class="item_img_size"><img src="<?php print h($img_dir . $value['img']); ?>"></sapn>
        <p><?php print h($value['name']); ?>
        <?php print h($value['price']); ?>円
          <div class="box11">
            <!--<p>メニュー内容</p>-->
          </div>
        <input type="hidden" name="sql_kind" value="add_product_to_cart"></p>
<?php if ($value['stock'] > 0) { ?>
        <input type="hidden" name="item_id" value="<?php print h($value['item_id']); ?>">
<?php 
} else {
?>
        <span>売り切れ</span>
<?php } ?>
      <div class="cart-btn">
        <input type="submit" value="カートに入れる">
      </div>
      </form>
<?php } ?>
<?php foreach ($err_msg as $value) { ?>
      <p><?php print h($value); ?></p>
<?php } ?>
   </div>
  </div>
  </main>
  <footer>
      <div class="footer-navi">
      <small>Copyright&copy;My Apron All Rights Reserved.</small>
    </div>
  </footer>
</body>
</html>