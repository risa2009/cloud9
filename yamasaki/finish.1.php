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

// 現在日時を取得
$now_date = date('Y-m-d H:i:s');
//まずはここからですね！
try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
 
  if($_SERVER['REQUEST_METHOD'] === 'POST') {
      //var_dump($_POST);
        //if($sql_kind === 'purchase'){
        //購入処理
        // 1. かーとから一覧を取得
        // 2. トランザクションの開始
        // 3. カートの購入数ぶんだけ在庫を減らす
        // 4. カートのデータを削除する
        // 5. 全部成功したらコミットする
        // ということになります。一旦はトランザクションを後回しにして、
        // 1,3,4だけ実装すればひとまず動作します。
        
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
    //あ、24行目から見てもらえますか？
    // SQL文を実行する準備        
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $user_id,    PDO::PARAM_INT);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $cart_data = $stmt->fetchAll();
            
          
    //UPDATEの実行
    //こちらですね！はい
    // まず、UPDATEではJOINはできません。JOINはSELECT文のみです。
    // 今回は先に取得したamountの値を用いて、計算した更新後の在庫数、
    // このコードでいう$changeの値にitems.stockを更新するだけです。
    //だとすると、UPDATE文はどうなりますか？（たんにitemsてーぶるのstockをupdateするだけです。）
    //ちょっとupdate文の文法を再確認してゼロから書いて見ましょう＾＾そんなに難しくありません。
    //惜しいですね！item_idを更新する必要はありますか？あ、ごめんなさい、itemsテーブルでしたね。
    //itemsテーブルで作成しますね。item_idは不要なのですね、
    //ちょっと考えて見てください。今回の処理でitemsテーブルのitem_idを変更する必要はありそうですか？ないです
    //はい、そうですね＾＾ですのでitem_idは更新しないでください。はい
    //そうですね＾＾はい、バッチリ。。あ、最後の条件(WHERE)は何を意味していますか？更新対象を絞り込むための条件なのでitem_idでした
    //はい！そうですね！これでバッチリです。さて、stockの更新後の値（在庫数）はどの変数に入っていましたか？
    //あらかじめ計算していただいていましたね！$cart_itemでしょうか。現在の在庫数から購入数を引いた値を計算していませんでしたか？
    //あ。そうですね！カートの購入数ぶんだけ在庫を減らす
    //65行目では何をしていますか？在庫から購入数を減らそうと思って書きました。
    //その後、在庫が更新されて、stockの更新後の値は$sqlにちょっと待ってください。はい＾＾
    //すみません、stockの更新後の値は$stmtに入ります。
    //はい、$stmtにbindValueされることになりますね！$stockはここまで登場していない変数です。
    //$stmt->bindValue(1, 更新後の値,     PDO::PARAM_INT);になる必要がありますね。更新後の値ですか。
    //そうですね。そもそもbindValueって何することですか？？に値を入れます。
    //はい、そうですね＾＾今回はstock = ? の?に値を入れるので、更新後の値を入れることになりますよね。はい
    //今在庫が5個で、購入数が2個なら?に入るのは3ですよね？はい
    //その計算、65行目ですでにしていませんか？してしまっていますね。
    //えっと、悪いことではないんです。そうではなく、すでに計算しているので、その値を使えませんか？$changeでしょうか。
    //はい！そうですね＾＾更新後の値、という意味で$changeにしていたのかと思っていました＾＾；そうですね、そのつもりでいたのですが
    // updateの処理方法など考えと異なることがあり、色々考えていました＾＾；
    //はい！こちらでオッケーですあとはbindValueが一つ多いですね！
    //はい、オッケーです。ここで、$item_idの値はどうなりますか？今回はPOSTで受け取っているわけではありませんね。
    //どこから$item_idの値を用意すれば良いでしょうか？
    //ヒントは、今まさに処理するこの商品のIDが欲しい、ということです。
    //まず、今まさに処理するこの商品のデータはどの変数に入っていますか？$cart_itemです
    //はい、そうですね＾＾ではこの商品のidはどうすれば取得できますか？この前やったやつですよね。ちょっとお待ちください。はい＾＾
    //例えば商品名は$cart_item['name']に入っていますね。はいそうですね＾＾ですので、この値をバインドしてあげましょう。
    //$cart_item['item_id']
    //はい、オッケーです！これで、カートに入った商品を一つづつ指定して、在庫を減らす処理完成です。
    //ついでに、せっかく作成していただいていた、（在庫が足りていれば）というif文も復活させましょう！
    //はい、オッケーです！
    //ちょっと移動しました。ありがとうございます。
    //振り返りやすいようにコメントつけますね。ありがとうございます
    //はい、これで各商品の在庫更新はバッチリです＾＾あとはカートから商品を取り除くところですが、
    //ちょっと下行きましょう。はい
              
    //カート内の各商品について
    foreach($cart_data as $cart_item){
      //在庫更新後の値を計算
      $change = $cart_item['stock'] - $cart_item['amount'];
            
      //在庫が0より小さくなければ（あ、私の勘違いでした＾＾；大丈夫でしょうか＾＾はい、大丈夫です＾＾）
      if($change >= 0) {//かっことじに全角があるようです。確認しましょう！
              
        //購入数（amount）のぶんだけ在庫を減らすUPDATE文を実行
        $sql = 'UPDATE items SET stock = ? WHERE item_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $change,                    PDO::PARAM_INT);
        $stmt->bindValue(2, $cart_item['item_id'],      PDO::PARAM_INT);
        $stmt->execute();
      }
    }
          
    //ここですね。インデントだけ直しましょう最後の閉じかっこにあわせるのですね。インデント
    //はい、そうです＾＾承知しました＾＾
    //これで完成しているはずですね！あとは表示ですが、あ、カートのままになっていますね
    //はい、それでほぼ大丈夫なのですが、foreachで繰り返し表示するデータが、
    //このファイルでは$rowsではなく最初に取得する$cart_dataになります。
    //171行目を書き換える必要がありますね！はい
    //カートの該当ユーザーのデータを消去するDELETE文
    //DELETEの実行
    $sql = 'DELETE FROM carts WHERE user_id = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $user_id,   PDO::PARAM_INT);
    $stmt->execute();
    //かっこを閉じるときは、常に今の高さより一つ左になります。
    //同じではないのですね。同じ位置で閉じるのかと思っていました。
    //今回で言えば135行の高さの一つ左になりますね。今までもそうしていらっしゃったと思います。はい
    //さて、続きに行きましょうはい、そうですね＾＾そこはtryを閉じる位置になるので一個余計でした。はい
    //残りも合わせましょう！
    //閉じかっこが右に行くのはおかしいですね！閉じかっこは今の高さより一つ左になります。
    //そうですね＾＾今の高さは143行目の高さなのでその左です。
    // 次に148行目はかっこの内側に入るので1つだけ右です。今は右に行きすぎですね
    //かっこの内側に入るときに一つ右、かっこを閉じるときに一つ左です。
    //厳格に守る必要があります。150行目は２つ右にいっています。はい、オッケーです＾＾
    //インデントは、「絶対に」守ることです。エラーを防ぐために、少しでもずれていたら気持ち悪い、
    //という感覚を持ちましょう。はい、今一度あわせかたを見直しておきます。
    //はい＾＾別に美学的なものではなく、効率よく開発するためのものです。
    //インデントを厳格に合わせるコストよりもバグを直す方が何十倍も時間がかかるので
    //エンジニアは必ず守ります。楽したいからなのです。＾＾基礎が大事ですね、書き終わったらしっかりあわせるようにします。
    //はい＾＾コピペしたら、その瞬間に直しましょう。あとで、は禁物です。
    //さて、改めて動作確認しましょう！はい
    //表示は元のままですので後から直していただきますが、カートの中身はちゃんと消えましたか？消えました！＾＾
    //良かったです＾＾あとは、在庫が減っているかどうかですね！これも確認して見てください。
    //元の在庫数がわからなかったので、ちょっと一連の流れてやってみます。
    //はい、承知しました！ではそちら確認できて、あと問題なさそうであればご連絡ください！
    //またサンプル提供させていただきたいです。その際にトランザクションの入れ方なども
    //サンプルを提供します。
    //あと、cart_sampleにuser_idの条件も加えているので、そこもチェックして頂ければと思います。
    //さて、これであとはログイン周りができればメインのフローが完成ですね！はい
    //ラストスパート頑張って行きましょう！ありがとうございました。トラザクションとuser_idの件承知しました、
    // トラザクションのことをうっかり忘れていましたが、無事に動くのですね。
    //はい、トランザクションは一部がエラーの時に動作しないようにする仕組みなので、なくても動作はします。
    //ただ、今回の場合で言えば、一部在庫切れでも購入処理が完了してしまうのです。
    //それを防ぐのがトランザクションになります。
    //まずはhtml部分の表示だけ整えて見てくださいね＾＾あとはサンプルでお見せします！はい、ありがとうございました！＾＾はい、ではまた後ほど！失礼します！
  }
  //}//かっことじ}の数がおかしいようですねちょっと上からインデント合わせて行きましょう。はい
  //一番上から見て行きましょう！
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
    <a class="nemu" href="#">ログアウト</a>
    <a href="https://risayamasaki-risayamasaki.c9users.io/MyApron/cart.php" class="cart"></a>
  </div>
  </header>
  <div class="cart_list">
    <h1>ご購入ありがとうございました</h1>
<!-- ここに個別のアイテムを記述 -->
  <div class="cart-list-title">
    <span class="cart-list-price">商品</span>
    <span class="cart-list-num">数量</span>
  </div>
  <!--はい、これでオッケーなはずです。確認してみましょう!-->
<?php foreach ($cart_data as $value)  { ?>
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
    <span class="buy-sum-price"><?php print h($value['price']); ?></span>
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