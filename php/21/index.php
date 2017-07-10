<?php 
// MySQL接続情報
$host     = 'localhost';
$username = 'risayamasaki';   // MySQLのユーザ名
$password = '';   // MySQLのパスワード
$dbname   = 'camp';   // MySQLのDB名
$charset  = 'utf8';   // データベースの文字コード

// MySQL用のDNS文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$sql     = '';
$img_dir = './img/';
$data    = [];
$err_msg = [];

try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  try {
    // SQL文を作成
    $sql = 'SELECT 
          drink_master.drink_id, 
          drink_master.drink_name, 
          drink_master.price,
          drink_master.img, 
          drink_stock.stock
        FROM drink_master JOIN drink_stock
        ON  drink_master.drink_id = drink_stock.drink_id
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

      $data[$i]['drink_id']    = htmlspecialchars($row['drink_id'],   ENT_QUOTES, 'UTF-8');
      $data[$i]['drink_name']  = htmlspecialchars($row['drink_name'], ENT_QUOTES, 'UTF-8');
      $data[$i]['price']       = htmlspecialchars($row['price'],      ENT_QUOTES, 'UTF-8');
      $data[$i]['img']         = htmlspecialchars($row['img'],        ENT_QUOTES, 'UTF-8');
      $data[$i]['stock']       = htmlspecialchars($row['stock'],      ENT_QUOTES, 'UTF-8');

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
  <meta charset="utf-8">
  <title>自動販売機</title>
  <style>
    #flex {
      width: 600px;
    }

    #flex .drink {
      //border: solid 1px;
      width: 120px;
      height: 210px;
      text-align: center;
      margin: 10px;
      float: left; 
    }

    #flex span {
      display: block;
      margin: 3px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .img_size {
      height: 125px;
    }

    .red {
      color: #FF0000;
    }

    #submit {
      clear: both;
    }

  </style>
</head>
<body>
<?php if (count($err_msg) === 0) { ?>
  <h1>自動販売機</h1>
  <form action="result.php" method="post">
    <div>金額<input type="text" name="money" value=""></div>
    <div id="flex">
<?php foreach ($data as $value)  { ?>
      <div class="drink">
        <span class="img_size"><img src="<?php print $img_dir . $value['img']; ?>"></span>
        <span><?php print $value['drink_name']; ?></span>
        <span><?php print $value['price']; ?>円</span>
<?php if ($value['stock'] > 0) { ?>
        <input type="radio" name="drink_id" value="<?php print $value['drink_id']; ?>">
<?php } else { ?>
        <span class="red">売り切れ</span>
<?php } ?>
      </div>
<?php } ?>
    </div>
    <div id="submit">
      <input type="submit" value="■□■□■ 購入 ■□■□■">
    </div>
  </form>
<?php } else { ?>
<?php foreach ($err_msg as $value) { ?>
    <p><?php print $value; ?></p>
<?php } ?>
<?php } ?>
</body>
</html>