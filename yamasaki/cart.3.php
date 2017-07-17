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
$item_id  = '';
$user_id  = 1;      //仮実装、ユーザーIDを１で固定
$total    = 0;
  // // 現在日時を取得
  // $now_date = date('Y-m-d H:i:s');
  
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
        $msg[] = '変更しました。';
      }else if($sql_kind === 'delete_cart_item'){
        // if(isset($_POST['delete_cart_item']) === TRUE){
        //   $delete_cart_item = $_POST['delete_cart_item'];
        // }
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
    
    //カートに入っている商品の単価と購入数量
    //こちらですね！はい
    //まず、foreachの利用まで思いついていただいているのはバッチリです！
    //まずはコメントアウトから復活させてあげましょう！
    //はい、そうですね！その上で、一つ一つ取り出した商品のデータは、どの変数に入りますか？
    //$rowsだと思っていましたが、$valueでしょうか？
    //まず、foreachは何をするものでしたっけ？繰り返しの処理です
    //はい！そうですね。そして、foreachの繰り返しは
    //「配列の要素を一つ一つ取り出し、それぞれの要素について処理を実行する」という繰り返しです。
    //ここで言えば 「foreach $rows」つまり、「$rowsのそれぞれの要素について」
    // 「as $row」すなわち 「$row として」取り出し、処理を繰り返し実行するということになります。
    //つまり、一つ一つ取り出した要素は$rowに入っています。よくわかりました！はい、そうですね！
    //ありがとうございます！あ、[]の使い方が全く違いますね。
    //$rows[0]→カートの1つめの商品のデータ
    //$rows[1]→カートの2つめの商品のデータ
    //$rows[2]→カートの3つめの商品のデータ
    //$rows[3]→カートの4つめの商品のデータ
    //と続いているものですが、それぞれの$rows[x]が$rowに一つ一つ取り出されていくことになります。
    //だとすればどうなりますか？これだと、1つめの商品の購入数に2つめの商品の金額をかけていることになりますね。
    //これだと1つめの商品の購入数に1つめの商品の金額をかけていますが、そもそも1つめの合計だけではなく
    //全ての商品の合計金額を出す必要がありますね！はい、合計ですよね、
    //そもそも$rowに取り出すためにforeachを使ったのですが、$rowが全く使われていません。
    //例えば1回目の繰り返しでは$row = $rows[0]になっています。
    //多分、何をしているのかわかっていませんね＾＾まずはこちらを見て見ましょう。はい
    //こちらで何が起こるか確認して見てください。はいカートに入っている購入数がみれました。そうですね！
    //こちらではどうでしょうか？カートに入っている商品名と購入数と価格が確認できました。
    //はい、そうですね！ではもう一度こちらにチャレンジできますか？$row =の部分は先ほどのトータルにして良いのでしょうか。
    //はい、合計金額を入れるための変数名であればオッケーです！はい
    //そうですね！ちなみに、繰り返しの中で合計を出すにはどうするんでしたっけ？基礎の繰り返しのところでやりましたね？
    //やりましたね。
    //ではヒントはこちらです。
    // foreach($rows as $row){
    //   print $row['name'] . '購入数:'. $row['amount'] . ' 価格' . $row['price'] . '<br>';
    // }
    //forでもwhileでもforeachでもこの{ }の中が繰り返されるのは全く一緒です。
      //あ、そのまま使ってくださいというわけではないです。if文があったのは3の倍数の時だけ
      //合計するからでしたね。理由のない利用はやめておきましょう。今回はすみません$i++
      //$totalに一つ一つの商品の購入金額（購入数 * 金額）を足して行きたいのです。
      //現在の処理では、$totalに繰り返しのたびに上書きで代入されています。
      //繰り返しが終わった後に残るのは最後の商品の購入金額だけになってしまいます。
      //どうすれば$totalに一つ一つの購入金額を足し込んでいけますか？++をつけたら良いでしょうか？
      //++は何のためにつけますか？まず++とは何をするものでしたか？
      //ちょっと、代入演算子の復習をしましょう。
      // まず $a = $a + n を略して書くことができるのが$a += n です。こちらは大丈夫ですか？はい
      //ありがとうございます！次にその中でも $a += 1 という処理は頻繁に使うので、
      //さらに省略した記法が $a++ です。改めて、今回、１を加えたい変数は何かありますか？いえ、ないです。すみません。
      //はい、そうですね＾＾では今もヒントがあったのですが、今回使う処理はどれでしょうか？
      //今回は$totalに合計金額をどんどん足しこみたいのです。$total = $total + 購入金額を繰り返したいのです。
      // $row['amount'] * $row['price'] が購入金額ですね！はい、では、書き換えて見ましょう！
      //はい、そうですね！そしてそれを省略した書き方もありましたね！はい、オッケーです！
      //これで、$totalはゼロから始まって、1こめの購入金額から順番に足されて行きます。ありがとうございました。
      //はい、ではこれで$totalに合計金額が入りましたので、200行目でこの$totalを表示してあげましょう！はい
    $total = 0;
    foreach($rows as $row){
      $total += $row['amount'] * $row['price'];
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
    <!--はい、そうですね＾＾では確認して見てください！-->
  　<!--しっかり合計額が表示されました！ありがとうございます-->
  　<!--良かったです＾＾では、まずはこちらはオッケーですね！はい。-->
  　<!--こちらも、よろしければまたみなさんにシェアしていただいてもよろしいですか？はいもちろんです-->
  　<!--＾＾ありがとうございます！では、多少整えてからまたサンプルとして出しますね！複製しておきます。はい-->
  　では続いて購入完了画面に行きましょう！はい
    <span class="buy-sum-price"><?php print h($total); ?></span>
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