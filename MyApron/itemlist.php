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

$data       = [];     // DBから取得した値を格納する配列
$err_msg    = [];     // エラーメッセージを格納する配列

$item_id    = '';

if (isset($_POST['item_id']) === TRUE) {
    $item_id = $_POST['item_id'];
  }
  
try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  
try {
  // SQL文を作成
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
    $rows = $stmt->fetchAll();
    // 1行ずつ結果を配列で取得します
    $i = 0;
    foreach ($rows as $row) {
      $data[$i]['item_id']   = htmlspecialchars($row['item_id'],   ENT_QUOTES, 'UTF-8');
      $data[$i]['name']      = htmlspecialchars($row['name'],      ENT_QUOTES, 'UTF-8');
      $data[$i]['price']     = htmlspecialchars($row['price'],     ENT_QUOTES, 'UTF-8');
      $data[$i]['img']       = htmlspecialchars($row['img'],       ENT_QUOTES, 'UTF-8');
      $data[$i]['status']    = htmlspecialchars($row['status'],    ENT_QUOTES, 'UTF-8');
      $data[$i]['stock']     = htmlspecialchars($row['stock'],     ENT_QUOTES, 'UTF-8');
      $i++;
 }
 $result_msg =  '追加成功';
} catch (PDOException $e) {
    // 例外をスロー
    throw $e;
}
} catch (PDOException $e) {
    $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。'.$e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>My Apron：商品一覧</title>
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
  <div class="item_list">
<?php if (count($err_msg) === 0) { ?>
<?php foreach ($data as $value)  { ?>
  <div class="item">
     <form action="cart.php" method="post">
　　　 <span class="item_img_size"><img src="<?php print $img_dir . $value['img']; ?>"></span>
　　　 <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
　　　 <span><?php print $value['name']; ?></span>
　　　 <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
　　　 <span><?php print $value['price']; ?>円</span>
　　　 <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
<?php if ($value['stock'] > 0) { ?>
<input type="checkbox" name="item_id" value="<?php print $value['item_id']; ?>" checked>
<?php 
} else {
?>
<span>売り切れ</span>
<?php } ?>
<input type="submit" value="カートに入れる">
     </form>
<?php foreach ($err_msg as $value) { ?>
     <p><?php print $value; ?></p>
<?php } ?>
  </div>
<?php } ?>
<?php } ?>
  </div>
</body>
</html>