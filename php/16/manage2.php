<?php
/* トランザクションの課題 */

// MySQL接続情報
$host     = 'localhost';
$username = 'root';   // MySQLのユーザ名
$password = '';       // MySQLのパスワード
$dbname   = 'camp';   // MySQLのDB名
$charset  = 'utf8';   // データベースの文字コード

// MySQL用のDNS文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$img_dir    = './img/';  // 画像のディレクトリ

$sql_kind   = '';     // SQL処理の種類
$result_msg = '';     // 実行結果のメッセージ
$data       = [];     // DBから取得した値を格納する配列
$err_msg    = [];     // エラーメッセージを格納する配列

// SQL処理を取得
if (isset($_POST['sql_kind']) === TRUE) {
  $sql_kind = $_POST['sql_kind'];
}

// 商品追加の場合は最初に入力項目をチェックする
if ($sql_kind === 'insert') {

  $new_name   = '';
  $new_price  = '';
  $new_stock  = '';
  $new_img    = '';

  if (isset($_POST['new_name']) === TRUE) {
    // 半角・全角空白のトリム
    $new_name = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $_POST['new_name']);
  }

  if (isset($_POST['new_price']) === TRUE) {
    // 半角・全角空白のトリム
    $new_price = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $_POST['new_price']);
  }

  if (isset($_POST['new_stock']) === TRUE) {
    // 半角・全角空白のトリム
    $new_stock = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $_POST['new_stock']);
  }

  //  HTTP POST でファイルがアップロードされたか確認
  if (is_uploaded_file($_FILES['new_img']['tmp_name']) === TRUE) {

    $new_img = $_FILES['new_img']['name'];

    // 画像の拡張子取得
    $extension = pathinfo($new_img, PATHINFO_EXTENSION);
    
    // 拡張子を小文字にする
    $extension = strtolower($extension);

    // 拡張子チェック
    if ($extension === 'jpg' || $extension == 'jpeg' || $extension == 'png') {

      // ユニークIDを生成し、保存ファイルの名前を変更する
      $new_img = md5(uniqid(mt_rand(), true)) . '.' . $extension;

      // 同名ファイルが存在するか確認
      if (is_file($img_dir . $new_img) !== TRUE) {

        // ファイルを移動し保存
        if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $new_img) !== TRUE) {
          $err_msg[] = 'ファイルアップロードに失敗しました';
        }

      } else {

        // 生成したIDがかぶることは通常ないため、IDの再生成ではなく再アップロードを促すようにする
        $err_msg[] = 'ファイルアップロードに失敗しました。再度お試しください。';
      }

    } else {
      $err_msg[] = 'ファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';
    }

  } else {
    $err_msg[] = 'ファイルを選択してください。';
  }

// 変更の場合の入力項目チェック
} else if ($sql_kind === 'update') {

  $update_stock = '';
  $drink_id     = '';

  if (isset($_POST['update_stock']) === TRUE) {
    // 半角・全角空白のトリム
    $update_stock = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $_POST['update_stock']);
  }

  if (isset($_POST['drink_id']) === TRUE) {
    $drink_id = $_POST['drink_id'];
  }
    
}

try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // 現在日時を取得
    $now_date = date('Y-m-d H:i:s');

    // 商品追加の場合
    if ($sql_kind === 'insert') {

      // トランザクション開始
      $dbh->beginTransaction();

      try {
        // SQL文を作成
        $sql = 'INSERT INTO test_drink_master (drink_name, price, img, create_datetime) VALUES (?, ?, ?, ?)';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $new_name,    PDO::PARAM_STR);
        $stmt->bindValue(2, $new_price,   PDO::PARAM_INT);
        $stmt->bindValue(3, $new_img,     PDO::PARAM_STR);
        $stmt->bindValue(4, $now_date,    PDO::PARAM_STR);
        // SQLを実行
        $stmt->execute();

        // INSERTされたデータのIDを取得
        $drink_id = $dbh->lastInsertId('drink_id');

        // SQL文を作成
        $sql = 'INSERT INTO test_drink_stock (drink_id, stock, create_datetime) VALUES (?, ?, ?)';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $drink_id,    PDO::PARAM_INT);
        $stmt->bindValue(2, $new_stock,   PDO::PARAM_STR);
        $stmt->bindValue(3, $now_date,    PDO::PARAM_STR);
        // SQLを実行
        $stmt->execute();
        // コミット
        $dbh->commit();
        // 表示メッセージの設定
        $result_msg =  '追加成功';

      } catch (PDOException $e) {
        // 例外をスロー
        throw $e;
      }
    } else if ($sql_kind === 'update') {
      try {
        // SQL文を作成
        $sql = 'UPDATE test_drink_stock SET stock = ?, update_datetime = ? WHERE drink_id = ?';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $update_stock,    PDO::PARAM_INT);
        $stmt->bindValue(2, $now_date,        PDO::PARAM_STR);
        $stmt->bindValue(3, $drink_id,        PDO::PARAM_INT);
        // SQLを実行
        $stmt->execute();
        // 表示メッセージの設定
        $result_msg = '在庫変更成功';
      } catch (PDOException $e) {
        // ロールバック処理
        $dbh->rollback();
        // 例外をスロー
        throw $e;
      }
    }
  }

  try {
    // SQL文を作成
    $sql = 'SELECT 
              test_drink_master.drink_id,
              test_drink_master.drink_name,
              test_drink_master.price,
              test_drink_master.img,
              test_drink_stock.stock
            FROM test_drink_master JOIN test_drink_stock
            ON  test_drink_master.drink_id = test_drink_stock.drink_id';
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();
    // 1行ずつ結果を配列で取得します
    $i = 0;
    foreach ($rows as $row) {
      $data[$i]['drink_id']   = htmlspecialchars($row['drink_id'],   ENT_QUOTES, 'UTF-8');
      $data[$i]['drink_name'] = htmlspecialchars($row['drink_name'], ENT_QUOTES, 'UTF-8');
      $data[$i]['price']      = htmlspecialchars($row['price'],      ENT_QUOTES, 'UTF-8');
      $data[$i]['img']        = htmlspecialchars($row['img'],        ENT_QUOTES, 'UTF-8');
      $data[$i]['stock']      = htmlspecialchars($row['stock'],      ENT_QUOTES, 'UTF-8');
      $i++;
    }

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
    section {
      margin-bottom: 20px;
      border-top: solid 1px;
    }

    table {
      width: 660px;
      border-collapse: collapse;
    }

    table, tr, th, td {
      border: solid 1px;
      padding: 10px;
      text-align: center;
    }

    caption {
      text-align: left;
    }

    .text_align_right {
      text-align: right;
    }

    .drink_name_width {
      width: 100px;
    }

    .input_text_width {
      width: 60px;
    }
  </style>
</head>
<body>
<?php if (empty($result_msg) !== TRUE) { ?>
  <p><?php print $result_msg; ?></p>
<?php } ?>
<?php foreach ($err_msg as $value) { ?>
  <p><?php print $value; ?></p>
<?php } ?>
  <h1>自動販売機管理ツール</h1>
  <section>
    <h2>新規商品追加</h2>
    <form method="post" enctype="multipart/form-data">
      <div><label>名前: <input type="text" name="new_name" value=""></label></div>
      <div><label>値段: <input type="text" name="new_price" value=""></label></div>
      <div><label>個数: <input type="text" name="new_stock" value=""></label></div>
      <div><input type="file" name="new_img"></div>
      <input type="hidden" name="sql_kind" value="insert">
      <div><input type="submit" value="商品を追加"></div>
    </form>
  </section>
  <section>
    <h2>商品情報変更</h2>
    <table>
      <caption>商品一覧</caption>
      <tr>
        <th>商品画像</th>
        <th>商品名</th>
        <th>価格</th>
        <th>在庫数</th>
      </tr>
<?php foreach ($data as $value)  { ?>
      <tr>
        <form method="post">
          <td><img src="<?php print $img_dir . $value['img']; ?>"></td>
          <td class="drink_name_width"><?php print $value['drink_name']; ?></td>
          <td class="text_align_right"><?php print $value['price']; ?>円</td>
          <td><input type="text"  class="input_text_width text_align_right" name="update_stock" value="<?php print $value['stock']; ?>">個&nbsp;&nbsp;<input type="submit" value="変更"></td>
          <input type="hidden" name="drink_id" value="<?php print $value['drink_id']; ?>">
          <input type="hidden" name="sql_kind" value="update">
        </form>
      <tr>
<?php } ?>
    </table>
  </section>
</body>
</html>