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

$item_id = '';

//セッション開始
session_start();

if(isset($_SESSION['user_id']) === TRUE){
  $user_id = $_SESSION['user_id'];
}else{
  header('location: login.php');//ログインしていなければ、ログイン画面へリダイレクト
}

// 現在日時を取得
$now_date = date('Y-m-d H:i:s');

try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
 
  if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = 'SELECT
                 carts.user_id,
                 carts.item_id,
                 carts.amount,
                 items.name,
                 items.price,
                 items.img,
                 items.stock
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

    //カート内の各商品について
    foreach($cart_list as $cart_item){
      //在庫更新後の値を計算
      $change = $cart_item['stock'] - $cart_item['amount'];
            
      //在庫が0より小さくなければ
      if($change >= 0) {
              
        //購入数（amount）のぶんだけ在庫を減らすUPDATE文を実行
        $sql = 'UPDATE items SET stock = ? WHERE item_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $change,                    PDO::PARAM_INT);
        $stmt->bindValue(2, $cart_item['item_id'],      PDO::PARAM_INT);
        $stmt->execute();
      }
    }
          
    //カートの該当ユーザーのデータを消去するDELETE文
    //DELETEの実行
    $sql = 'DELETE FROM carts WHERE user_id = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $user_id,   PDO::PARAM_INT);
    $stmt->execute();
  }
} catch (PDOException $e) {
  echo 'データベース処理でエラーが発生しました。理由：'.$e->getMessage();
}

//合計金額の計算処理
$total = 0;
foreach($cart_list as $cart_item){
  //購入数 * 単価を合計していく
  $total += $cart_item['amount'] * $cart_item['price'];
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
  <title>My Apron | 購入完了</title>
</head>
<body>
  <header>
    <div class="container">
      <ul class="main-nav">
        <li>
          <a class="shop-title" href="login.php">My Apron</a>
        </li>
        <li>
          <a class="logout-menu" href="logout.php">ログアウト</a>
        </li>
      </ul>
    </div>
  </header>
  
  <section>
    <div class="container">
      <div class="cart_list">
      <h1>お買い上げありがとうございました</h1>
        <table class="cart-table">
          <tr>
            <th>画像</th>
            <th>商品名</th>
            <th>金額</th>
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
          </tr>
<?php } ?>
        </table>
        <div class="buy-sum-box">
          <span class="buy-sum-title">合計:</span>
          <span class="buy-sum-price"><?php print h($total); ?>円</span>
        </div>
      </div>
    </div>
  </section>
  
  <footer>
    <small>Copyright&copy;My Apron All Rights Reserved.</small>
  </footer>
</body>
</html>