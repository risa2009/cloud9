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
$data = [];   // DBから取得した値を格納する配列
$err_msg = [];   // エラーメッセージを格納する配列

$item_id = '';
$user_id    = 1;      //仮実装、ユーザーIDを１で固定

// if (isset($_POST['user_id']) === TRUE) {
//     $item_id = $_POST['user_id'];
//   }
  
// SQL処理を取得
if (isset($_POST['sql_kind']) === TRUE) {
  $sql_kind = $_POST['sql_kind'];
}
  // 現在日時を取得
  $now_date = date('Y-m-d H:i:s');

 try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //カート内のデータを一覧表示するのはPOSTされた時だけですか？
    //このカートのページを開くと、カートの中の商品一覧が表示されますよね？
    //ということは、POSTされていなくても、商品一覧が表示される必要があります。
    //ですので、一覧取得のSELECT文はこのif文が終わった後になります。
    //POSTされた時だけ行う処理は、「在庫数の変更」と「カートからの削除」です。

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      if($sql_kind === 'change_amount'){
        //在庫数の変更処理
      }else if($sql_kind === 'delete_cart_item'){
        //カートからの削除処理
      }
      
    }
    //select文でカート内のデータを取得
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
    $rows = $stmt->fetchAll();
    
    //var_dump($rows);
      
      // 1行ずつ結果を配列で取得します←このコメントが嘘つきなのが原因ですね＾＾
      //ここからの処理は、結果を配列で取得する処理ではなく、
      //すでに$rowsに取得できている配列をhtmlspecialcharsでエスケープした
      //新しい配列$dataに移し替える（$rowsの中身が消えるわけではありません）処理です。
      //まず、今回htmlの方でforeachでループさせているのは$dataではなく、$rowsでしたので
      //この処理は全く使われていないことになります。
      //また、先ほどと同じように、h関数を使ってprintするときにエスケープすれば良いので、この処理は
      //どちらにしても不要になります。
      // $i = 0;
      // foreach ($rows as $row) {
      //   $data[$i]['item_id']   = htmlspecialchars($row['item_id'],   ENT_QUOTES, 'UTF-8');
      //   $data[$i]['amount']    = htmlspecialchars($row['amount'],      ENT_QUOTES, 'UTF-8');
      //   $data[$i]['name']      = htmlspecialchars($row['name'],     ENT_QUOTES, 'UTF-8');
      //   $data[$i]['price']     = htmlspecialchars($row['price'],       ENT_QUOTES, 'UTF-8');
      //   $data[$i]['img']       = htmlspecialchars($row['img'],    ENT_QUOTES, 'UTF-8');
      //   $i++;
      // }  
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
  <link rel="stylesheet" href="MyApron.css">
</head>
<body>
  <header>
  <div class="header-box">
    <a href="https://risayamasaki-risayamasaki.c9users.io/MyApron/itemlist.php">
    <img class="logo" src="./img/logo.png" alt="MyApron">
    </a>
  </div>
  </header>
  <div class="cart_list">
<!-- ここに個別のアイテムを記述 -->
  <div class="cart">
  <table>
    <tr>
      <th>商品</th>
      <th>商品名</th>
      <th>単価</th>
      <th>数量</th>
      <th>小計</th>
    </tr>
<?php foreach ($rows as $value)  { ?>
    <tr>
      <form action="cart.php" method="post">
      <!--  macならcommandと/です。この行で試してみましょう！コメント解除も同じコマンドです。-->
    <td><span class="item_img_size"><img src="<?php print $img_dir . h($value['img']); ?>"></span></td>
    <td><span><?php print h($value['name']); ?></span></td>
    <td><?php print h($value['price']); ?>円</td>
    <input type="hidden" name="user_id" value="<?php print h($value['user_id']); ?>">
   </form>
   </tr>
   <tr><td colspan='2'> </td><td><strong>合計</strong></td><td>円</td></tr>
  </table>
  
<?php } ?>
      <form action="finish.php" method="post">
        <input type="submit" value="購入する">
      </form>
    </div>
  </div>
</body>
</html>