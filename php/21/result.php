<?php
// MySQL接続情報
$host     = 'localhost';
$username = 'risayamasaki';   // MySQLのユーザ名
$password = '';   // MySQLのパスワード
$dbname   = 'camp';   // MySQLのDB名
// MySQL用のDNS文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host;

$drink_id = '';
$money    = '';
$sql      = '';
$img_dir  = './img/';
$data     = [];
$err_msg  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (isset($_POST['money']) === TRUE) {
    $money = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $_POST['money']);
  }

  if (isset($_POST['drink_id']) === TRUE) {
    $drink_id = $_POST['drink_id'];
  }

  if ($money === '') {
    $err_msg[] = 'お金を投入してください。';
  } else if (preg_match('/\A\d+\z/', $money) !== 1 ) {
    $err_msg[] = 'お金は半角数字を入力してください';
  } else if ($money > 10000) {
    $err_msg[] = '投入金額は1万円以下にしてください';
  }
  
  if ($drink_id === '') {
    $err_msg[] = '商品を選択してください';
  } else if (preg_match('/\A\d+\z/', $drink_id) !== 1 ) {
    $err_msg[] = 'エラー: 不正な入力値です';
  }
  
  if (count($err_msg) === 0) {

    try {
      // データベースに接続
      $dbh = new PDO($dsn, $username, $password);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

      // SQL文を作成
      $sql = 'SELECT 
           drink_master.drink_id, 
           drink_master.drink_name, 
           drink_master.price,
           drink_master.img, drink_stock.stock
          FROM drink_master JOIN drink_stock
          ON  drink_master.drink_id = drink_stock.drink_id
          WHERE drink_master.drink_id = ?';
      // SQL文を実行する準備
      $stmt = $dbh->prepare($sql);
      // SQL文のプレースホルダに値をバインド
      $stmt->bindValue(1, $drink_id,    PDO::PARAM_INT);
      // SQLを実行
      $stmt->execute();
      // レコードの取得
      $rows = $stmt->fetchAll();

      // 在庫があるかをチェック
      if ($rows[0]['stock'] > 0) {

        $change = $money - $rows[0]['price'];

        if ($change >= 0) {

          // 現在日時を取得
          $now_date = date('Y-m-d H:i:s');

          // トランザクション開始
          $dbh->beginTransaction();
          try {
           // SQL文を作成
           $sql = 'UPDATE drink_stock SET stock = ?, update_datetime = ? WHERE drink_id = ?';
           // SQL文を実行する準備
           $stmt = $dbh->prepare($sql);
           // SQL文のプレースホルダに値をバインド
           $stmt->bindValue(1, ($rows[0]['stock'] - 1),    PDO::PARAM_INT);
           $stmt->bindValue(2, $now_date,       PDO::PARAM_STR);
           $stmt->bindValue(3, $drink_id,       PDO::PARAM_INT);
           // SQLを実行
           $stmt->execute();

           // SQL文を作成
            $sql = 'INSERT INTO drink_history(drink_id, update_datetime) VALUES(?, ?)';
           // SQL文を実行する準備
           $stmt = $dbh->prepare($sql);
           // SQL文のプレースホルダに値をバインド
           $stmt->bindValue(1, $drink_id,    PDO::PARAM_INT);
           $stmt->bindValue(2, $now_date,    PDO::PARAM_STR);
           // SQLを実行
           $stmt->execute();

           // コミット
           $dbh->commit();
          } catch (PDOException $e) {
            // ロールバック処理
            $dbh->rollback();
            // 例外をスロー
            throw $e;
          }

          // 表示データの設定
          $data['drink_name'] = htmlspecialchars($rows[0]['drink_name'], ENT_QUOTES, 'UTF-8');
          $data['price']      = htmlspecialchars($rows[0]['price'],      ENT_QUOTES, 'UTF-8');
          $data['img']        = htmlspecialchars($rows[0]['img'],        ENT_QUOTES, 'UTF-8');
          $data['change']     = htmlspecialchars($change,            ENT_QUOTES, 'UTF-8');
        } else {
          $err_msg[] = 'お金がたりません！';
        }
      } else {
        $err_msg[] = '売り切れです！';
      }
    } catch (PDOException $e) {
      $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。'.$e->getMessage();
    }
  }
} else {
  $err_msg[] = '不正なアクセスです';
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>自動販売機結果</title>
</head>
<body>
  <h1>自動販売機結果</h1>
<?php if (count($err_msg) === 0) { ?>
    <img src="<?php print $img_dir . $data['img']; ?>">
    <p>がしゃん！【<?php print $data['drink_name']; ?>】が買えました！</p>
    <p>おつりは【<?php print($money - $rows[0]['price']); ?>円】です</p>
<?php } else { ?>
<?php foreach ($err_msg as $value) { ?>
    <p><?php print $value; ?></p>
<?php } ?>
<?php } ?>
  <footer><a href="index.php">戻る</a></footer>
</body>
</html>
