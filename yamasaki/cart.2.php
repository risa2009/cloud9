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
$user_id    = 1;      //仮実装、ユーザーIDを１で固定

  // // 現在日時を取得
  // $now_date = date('Y-m-d H:i:s');
  
  try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      var_dump($_POST);
   //あとは、購入数の更新では、$_POST['amount']も受け取ることになりますね。
   //ここでsql_kindも受け取りましょう！あ、はいそうですね！では、確認してみましょう！
      
      if(isset($_POST['sql_kind']) === TRUE){
          $sql_kind= $_POST['sql_kind'];
      }
      
      if(isset($_POST['item_id']) === TRUE){
        $item_id = $_POST['item_id'];
      }
      
      if($sql_kind === 'change_amount'){
        //購入数の変更処理
        if(isset($_POST['change_amount']) === TRUE){
          $amount = $_POST['change_amount'];
        }
        var_dump($amount);
        $sql = 'UPDATE carts SET amount = ? WHERE item_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $amount,     PDO::PARAM_INT);
        $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
        $stmt->execute();
        $msg[] = '変更しました。';
        //hiddenの使い方でしたね。
        //まず、今回はhiddenを何のために使うかというと、
        // 1. 行う処理がカートからの削除なのか、購入数の更新なのかを分岐する
        // 2. どの商品のボタンが押されたのかを識別する
        //この２つになります。
        //次に、それをどう使うかというと、1番は$sql_kindという変数に受け取って、
        // if文で分岐させます。
        //次に、どの商品のボタンが押されたのかを識別するには$item_idという変数に受け取ることになります。
        //従って、sql_kindというnameのhiddenのinputと、item_idというnameのhiddenのinputを送信することになります。
      }else if($sql_kind === 'delete_cart_item'){
        // if(isset($_POST['delete_cart_item']) === TRUE){
        //   $delete_cart_item = $_POST['delete_cart_item'];
        // }
        var_dump('test');
        //カートからの削除処理
        $sql = 'DELETE FROM carts WHERE item_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $item_id,   PDO::PARAM_INT);
        $stmt->execute();
        $msg[] = '削除しました。';
      }
    }
    
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
  <title>My Apron：ショッピングカート</title>
  <link rel="stylesheet" href="MyApron.css">
</head>
<body>
  <header>
  <div class="header-box">
    <a href="https://risayamasaki-risayamasaki.c9users.io/MyApron/itemlist.php">
    <img class="logo" src="./img/logo.png" alt="MyApron">
    </a>
    <a class="nemu" href="#">ログアウト</a>
    <a href="https://risayamasaki-risayamasaki.c9users.io/MyApron/cart.php" class="cart"></a>
  </div>
  </header>
  <div class="cart_list">
    <h1>ショッピングカート</h1>
<!-- ここに個別のアイテムを記述 -->
  <div class="cart-list-title">
    <span class="cart-list-price">商品</span>
    <span class="cart-list-num">数量</span>
  </div>
<?php foreach ($rows as $value)  { ?>
<ul class="cart-list">
  <li>
    <div class="cart-item">
      <!--ちなみに、この辺りは本当はtableタグを使った方が-->
      <!--レイアウトしやすいところですね。まあ、今は仕上げるのが最優先なのでこのままでいいでしょう。-->
 
      <span class="item_img_size"><img src="<?php print $img_dir . h($value['img']); ?>"></span>
      <span><?php print h($value['name']); ?></span>
      <form action="cart.php" method="post">
        <input type="text"  class="input_text_width text_align_right" name="change_amount" value="<?php print $value['amount']; ?>">個&nbsp;&nbsp;<input type="submit" value="変更">
        <input type="hidden" name="item_id" value="<?php print h($value['item_id']); ?>">
        <input type="hidden" name="sql_kind" value="change_amount">
      </form>
      <form action="cart.php" method="post">
        <input type="submit" value="削除">
        <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
	    　<input type="hidden" name="sql_kind" value="delete_cart_item">
      </form>
      <span class="cart-item-price"><?php print h($value['price']); ?>円</span>
        
    
    
    </div>
  </li>
</ul>
<?php } ?>
<div class="buy-sum-box">
　<span class="buy-sum-title">合計</span>
　  <!-- ★C-3-2 ●ショッピングカートにある商品の合計を表示する。-->
    <!-- ここから入力 -->
    <span class="buy-sum-price">¥1000</span>
    <!-- ここまで入力 -->
</div>
    <div>
      <!-- ★C-4 商品を購入する（「購入完了ページページ」に遷移する）。-->
      <form action="finish.php" method="post">
        <input class="buy-btn" type="submit" value="購入する">
      </form>
    </div>
    </div>
  </div>
</body>
</html>