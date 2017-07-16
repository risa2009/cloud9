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
    //ここはなんのif文ですか？これはポストからの受け取りの部分ですが、
    //カート内のデータを一覧表示するのはPOSTされた時だけですか？ごめんなさいちょっとわからなくなってきてしまいました
    //なるほど＾＾まず、このカートのページを開くと、カートの中の商品一覧が表示されますよね？はい
    //ということは、POSTされていなくても、商品一覧が表示される必要があります。
    //ですので、一覧取得のSELECT文はこのif文が終わった後になります。
    //POSTされた時だけ行う処理は、「在庫数の変更」と「カートからの削除」です。はい！
    //一旦コメントだけ残しておいて、select文をifの外に出しましょう！
    //はい！コメントを残しておいたので、SELECT文を移動してあげてください！トライのしたですか？
    //いえ、POSTされていなくても、ということなので46行目のif文が閉じた直後になります。
    
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      if($sql_kind === 'change_amount'){
        //在庫数の変更処理
      }else if($sql_kind === 'delete_cart_item'){
        //カートからの削除処理
      }
      
    }
    //はい！そうですね！インデントを直しましょう！ありがとうございます。
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
    
    //あとはここですね！先ほどのitem_listと同じですが
    //覚えていますか？配列取得の部分でしょうか？ここは悩んだのですが表示にしました。
    //表示にした、とはどういうことですか？取得するようにしました。と言う意味でした！
    //なるほど、86行目から、95行目までの処理をすることにした、ということですか？そうです
    //なるほど。その理由はなぜでしたか？foreachの中身がないというエラーだと思いましたので、取得する必要があるのかと思いました。
    //なるほど！わかりました。まずは
    var_dump($rows);
      
      // 1行ずつ結果を配列で取得します←このコメントが嘘つきなのが原因ですね＾＾
      //ここからの処理は、結果を配列で取得する処理ではなく、
      //すでに$rowsに取得できている配列をhtmlspecialcharsでエスケープした
      //新しい配列$dataに移し替える（$rowsの中身が消えるわけではありません）処理です。
      //まず、今回htmlの方でforeachでループさせているのは$dataではなく、$rowsでしたので
      //この処理は全く使われていないことになります。
      //また、先ほどと同じように、h関数を使ってprintするときにエスケープすれば良いので、この処理は
      //どちらにしても不要になります。わかりました！ありがとうございます。
      //オッケーです！あとは先ほどの復習をしていきましょう!catch節に飛びましょう
      // $i = 0;
      // foreach ($rows as $row) {
      //   $data[$i]['item_id']   = htmlspecialchars($row['item_id'],   ENT_QUOTES, 'UTF-8');
      //   $data[$i]['amount']    = htmlspecialchars($row['amount'],      ENT_QUOTES, 'UTF-8');
      //   $data[$i]['name']      = htmlspecialchars($row['name'],     ENT_QUOTES, 'UTF-8');
      //   $data[$i]['price']     = htmlspecialchars($row['price'],       ENT_QUOTES, 'UTF-8');
      //   $data[$i]['img']       = htmlspecialchars($row['img'],    ENT_QUOTES, 'UTF-8');
      //   $i++;
      // }  
      //はい！そうですね＾＾
      //あとはforeachの中身をprintするときにh関数を使ってあげましょう！
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
      <!--はい！そうですね＾＾さらに、ここは一つ一つ購入ボタンを設定していらっしゃいますが、-->
      <!--ここでは購入するのではなく、購入数の変更と、カートからの削除ですね！-->
   
      <!--formは必要ですよ！ありがとうございます。-->
      <!--そして、action先は、finish.phpではなく、cart.phpになります。-->
      <!--そして、最後に、全ての商品のデータが表示された後に、購入ボタンですので、-->
      <!--foreachの後にfinish.phpへのactionが設定された購入ボタンがあることになります。-->
      <!--インデントもありがとうございます！購入するボタンの方にfinishなのですね、先ほどもaction="ltemlist.php" が消えているなと思いました。-->
      <!--（サンプルの方で）はい、これは、action先を設定しないと、「自分自身のファイルにデータを送信する」という特徴を利用しました。-->
      <!--サンプルではファイル名が変わるので、元のファイルとサンプルで記述を変える必要がないようにしていました。-->
      <!--actionを設定していただいてももちろん構いません。そうだったのですね！自分自身のファイルにデータを送信すると言う特徴、、、-->
      <!--ちょっとまたあとで確認してみます。-->
      <!--はい＾＾では、一旦表示した上で、教科書のサンプルと比較して、注文数の変更のフォームと-->
      <!--削除ボタンを作っていきましょう。サンプルの「ソースを表示」を行うと-->
      <!--どんな作りになっているかすぐに確認できます。まずはこのファイルが正しく表示されることを-->
      <!--確認してからそちらに移りましょう！はい表示されました＾＾！ありがとうございます。-->
      <!--はい＾＾良かったです。あとはthが一つ足りないようですね！画像の列のぶんだけずれています。-->
      <!--追加しちゃいましょう！追加しました＾＾ちょっとずれていますが、あとで見直します！<!--をすぐにどうやって出されているのですか？-->
    <!--  macならcommand + / winなら ctrl + /です。この行で試してみましょう！コメント解除も同じコマンドです。あ、文字がでかくなりました。-->
    <!--  文字サイズの設定を一度クラウド９で変えたことがあったので、その時に別の設定にしてしまったかもです。すみません！-->
    <!--  多分　commandと+を押しませんでしたか？そうではなく、commandと/です。ようやくできました！はい＾コメント解除もしてみましょう！同じコマンドです。-->
  　　<!--ありがとうございます。commandと+を押してました-->
   <!--  ＾＾失礼します！＾＾-->

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