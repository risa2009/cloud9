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
$user_id    = 1;

//$amount     = 1;


//ログインチェックの処理
// if(isset($_SESSION['user_id']) === TRUE){
//   $user_id = $_SESSION['user_id']
// }else{
//   header('location: login.php');//ログインしていなければ、ログイン画面へリダイレクト
// }

if (isset($_POST['item_id']) === TRUE) {
    $item_id = $_POST['item_id'];
  }
  // 現在日時を取得
  $now_date = date('Y-m-d H:i:s');
  
  try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    //まずはここでPOSTの場合の処理となります。if文を追加してみましょう！
    //オッケーです！ではこちらでSELECTからのINSERT or UPDATEの処理を行いましょう。
    //70行目からの部分ですね
    //はい、まずはSELECTの部分ですね！ここではSELECT文を実行してデータを取得します。
    //ですので、executeした後にfetchAllが必要になります。付け加えてみましょう！SELECT後につけてみました！
    //ありがとうございます！では、まずこの取得したデータは、carts内のデータなので
    //名前は$rowsではなく$cart_listにしておきましょう。はい変更しました
    
    //はい、ここでPOSTチェックの仕方に問題がありますね。$_SERVERの値のはずが少し変になっています。
    //REQUEST_METHODは今の書き方だと定数になってしまいます。これは文字列ですね。
    //はいオッケーです！動作チェックしましょう！ありがとうございます。表示確認できました＾＾
    //良かったです＾＾では、改めてカートの処理に入って行きましょう。はい 76行目からですね。
    if($_SERVER['REQUEST_METHOD'] === 'POST') {//まずはif文の中をインデントです。はい
      //次に、99行に行きましょう
      // 指定された商品idと現在のユーザーidをWHEREの条件にしてcartテーブルをSELECT
      //まず、ここで$sqlという変数にSQL文の文字列を代入し
      $sql = 'SELECT
                 carts.user_id,
                 carts.item_id,
                 carts.amount
            FROM carts JOIN items
            ON carts.item_id = items.item_id
            WHERE carts.item_id = ?
            AND user_id = ?';
  
      // SQL文を実行する準備
      //ここです！ここでデータベースに一部虫食いのSQL文をプリペア（準備）しています。
      //そして、このデータベースに準備されたSQLをプリペアドステートメント(Prepared Statement)と言います。
      //$stmtはstatementを表す略字で、まさにこのプリペアドステートメントを表しています。はい
      // さて、先ほどの$stmtですが、抜けていますね。こちらが
      //はい、そうです！では治してみましょう！はい
      $stmt = $dbh->prepare($sql);
      
      // SQL文のプレースホルダに値をバインド
      $stmt->bindValue(1, $item_id,    PDO::PARAM_INT);
      $stmt->bindValue(2, $user_id,    PDO::PARAM_INT);
  
      // SQLを実行
      $stmt->execute();
      
      // レコードの取得
      $cart_list = $stmt->fetchAll();
      //はい、ここで$cart_listを取得したので中身を確かめましょう。
      //これでもう一度実行してみてください。そして、カートに入れるボタンを押してみましょう。押してみましたカートに入っていないですね
      //ありがとうございます。何も取得できていませんね。これはまだカートに何も入っていないので
      //正しい動作です。
      //では、一度1番のユーザーがハンバーグをカートに入れたデータをphpmyadminから追加してみていただけますか？はい！
      var_dump($cart_list);
      //できましたか？お待たせしました。今まで入ってたデータがたくさんあったので、ちょっと消してましたできました！
      //ありがとうございます！ではちょっとフォームの作りに問題がありそうなので、htmlのフォームのところに行きましょう。はい
      
      // はい、ではカートの中身があるかないかで分岐させて行きましょう。はい
      //単純なif文です。これで、カートが取得できて入れば行数が1より大きくなるので
      //たったこれだけで、cartに中身があるかないかは判定できます。ありがとうございます！なんと。。
      //ということです。はい、とてもシンプルですね。
      //はい＾＾ですのでそれぞれ該当の部分をifとelseのブロックにそれぞれ入れてみましょう。
      //あ、コメントアウトされているものをコピペするだけでいいですよ。
      //はい、そうですね！あとはちょっと治して行きましょう。はい
      if(count($cart_list) > 1){
        //カートに入っているのでupdate
        //まずはインデントの修正です。かっこの中身は一つインデントなので
        //はい、ここでは?が2つありますがbindValueが一つだけで、2個目の?がbindValueされていませんね！
        //本当ですね
        //あとは、1こめの?のところもエラーの原因ではないですが、１個多くなってしまうので消しておきましょう。バッチリです！
        //では、item_idのあたいもbindしちゃいましょう！はい＾＾
        $sql = 'UPDATE carts
          SET amount = ?
          WHERE item_id = ?';
         
        //ここのamountですが、まずsqlの中で計算するよりも、計算してからbindValueしてしまう方が良いですね。
        //その上で、$amountの値は、$cart_listで取得した値に含まれています。
        //今回、$cart_listは今後の動作が正しくなって入れば、特定のユーザーの特定の商品のカートデータは
        //1件だけになります。
        //ですので$cart_listの0番目に該当のカートデータが入っていて、そこのamountの値を取得すればオッケーです。
        //つまり$cart_list[0]['amount']に欲しい値が入っています。ここまではいかがですか？はい、わかりました
        //ありがとうございます！では、今回設定する$amountの値はどう計算できますか？すみません！ちょっとわからないです、、
        //ありがとうございます。まず、$cart_list[0]['amount']に入っている数字は、「現在カートに入れている購入数」です。はい
        //では、カートに5個入っている状態で「カートに入れるボタン」を押したら、カートにいくつ入れたことになって欲しいですか？6こです
        //そうですね！そして$cart_list[0]['amount']には5という数字が入っています。そうしたらどうすればいいですか？+1をしたいです
        //そうですね！しましょう！なるほど、これだと$cart_listの1番目になってしまいますね。
        //そうではなく、$cart_list[0]['amount']に1を足しましょう。
        //これだと$cart_list[0]['amount1']を取得することになりますね。
        //そうではなく$cart_list[0]['amount']が5なのです。はい！そうです！すみません！
        //これで、正しく今回設定したい$amountの数字が計算できました。こちらはこれでオッケーです。
        //次にinsertの方も見てみましょうはい
        
        $amount = $cart_list[0]['amount'] +1;
        
        //はい、ここでエラーが出ていますね！理由はわかりますか？なぜでしょうか？117行めの$amountは関係ないですか？
        //エラーメッセージを読みましょう！なんと書いてありますか？
        //Parse error: syntax error, unexpected '$stmt' (T_VARIABLE) です。$stmtの構文エラーとなっています。
        //はい、そうですね！unexpectedつまり、予想外の$stmtが出てきたと。この$stmtってなんですか？
        //これはcart_listに上で置き換わっているからでしょうか、なに、というのがうまく説明できません。すみません！
        //ありがとうございます。では実際に使われているところを見てみましょうはい57行目に行きましょう。はい
        //はい！そうですね！虫食いのSQL文をプリペアして、虫食いの部分に$amountがバインドされています。
        // これでオッケーです。実行してみましょう！
        //はい、またエラーになっていると思いますが、これはケアレスです。124行目の行末をみてください。
        //はい！もう一度チェックしましょう！
        //またエラーになっているのは確認できましたか？はい、
        //amountが1いじょう入らないのでしょうか。商品を初めてにカートに入れる時も１回では入らず２回目で入る感じでしょうか。
        //ありがとうございます。そうではありません。まず、エラーメッセージですが、
        // データベース処理でエラーが発生しました。理由：SQLSTATE[HY093]: Invalid parameter numberと出ています。
        // Invalid parameter numberとは、バインドする変数の数がおかしいということです。
        //sqlをみてみましょう上のところです。103行目に行きましょう。
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $amount,     PDO::PARAM_INT);
        //オッケーです！また確認しましょう！カートに商品が正しく入ります！！＾＾ありがとうございます！！
        //はい、ありがとうございます！今はカート内のデータが同じユーザーの同じ商品データが複数入ってしまっていますので、
        //一旦カート内のデータを全削除してからテストしてみましょう。
        //そして、今回最も大事なのは、エラー修正のプロセスと、制作のプロセスです。
        //まず、制作に関してはまず大枠の流れを決めて、そこから一つ一つの処理を書いていくこと。
        //エラー修正に関しては、エラーメッセージの内容を読む前に修正内容を決めないことです。
        //エラーメッセージが読めない→おかしそうなところを修正してみる、ではなく
        //エラーメッセージが読めない→検索or質問でエラーメッセージの内容を理解する
        //→問題の原因を突き止める→原因を取り除く
        //という手順にしましょう。怪しいところを当てずっぽうで修正すると、治っても理由がわからないままになります。
        //慣れるとすぐにできますし、質問すれば一瞬で答えてもらえる今がチャンスです！
        //ありがとうございます。あたらめて制作の手順を確認させていただけたこと、エラーを確認して該当箇所を修正していくこと
        //を一連の流れで教えていただけて、ほんとうによかったと思っています。
        //ありがとうございます＾＾では、このファイル、コメントが多すぎて読みにくいので
        
        $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
        $stmt->execute();
        // update文では、結果を取得するわけではないのでfetchallは不要です。はい
      
      }else{
        //カートに入っていないので、insert
        //こちらも治してみてください！
        //はい、ありがとうございます。あとは$amountの値です。
        //はい
        //$amountは上で1に固定していますが、これはそれぞれ別々の値になるので
        //上では初期化しないでおきましょう。ちょっとコメントアウトしてきてもらえますか？はいOKです
        //ありがとうございます！ではまずupdateの場合から見てみましょう。
        
        //はい、こちらですね。こちらは$amountにいくつを設定したいですか？
        //先ほどの例からの続きと考えて良いのでしょうか？
        //仮にそうだとするとどうなりますか？6でしょうか。あ、そういう意味ですね。
        //今回、なぜUPDATEではなくINSERTするんでしたっけ？はい、大丈夫です＾＾
        //カートに入れるためと思っていましたが、ちょっと待ってください。
        //カートに入っていないので、insertでしたね。$amountを1としたいです。すみません！
        //はいバッチリです！そうしましょう！
        //あ、$amountを利用しているのはどこですか？
        
        $amount = 1;//オッケーです！
        //ちなみに今回はこれでオッケーですがもう一つ$stmt->bindValue(3, 1, PDO::PARAM_INT);としても良いです。
        //今回はこれで行きましょう。はい！ありがとうございます。
        //はい＾＾これで、selectしてinsert or updateのところが出来上がりました。動作チェックしてみましょう！はい
        
        $sql =  'INSERT INTO carts (user_id, item_id, amount, create_datetime) 
                VALUES (?, ?, ?, ?)';
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $user_id,    PDO::PARAM_INT);
        $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
        $stmt->bindValue(3, $amount,     PDO::PARAM_INT);
        $stmt->bindValue(4, $now_date,   PDO::PARAM_STR);
        
        $stmt->execute();
      }

        
    }//ここがPOSTの閉じですね。はい
    //ここからはSELECT文で一覧表示用のデータを取得します。
    //124行目に行きましょう
    
    // // 公開商品のみ表示
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
    $item_list = $stmt->fetchAll();
    //はい、ここで取得しているのは商品の一覧ですので、例えば$item_listなどにしておきましょう。
    //オッケーです！あとは表示部分ですので、htmlの中のforeachに行きましょう！
    
    
    
    // //ありがとうございます！まず、ここで一覧表示用のデータが$rowsに代入されています。はい
    // //ところが、84行目に行ってみましょう
    //ここからは、すでに上にコピーしたところですので消して行きましょう。
    //あ、私のコメントなども問題なければ消しちゃってください＾＾コピーしておきますね。はい＾＾みやすくなりました
    //そうですね！では、ここの$rowsから$dataに移す処理ですが、実は不要です。
    //$rows自体がすでに結果の配列になっているのであとは出力時にhtmlspecialcharsをかけるだけで良いのです。
    //ただ、htmlspecialcharsも毎回かくと面倒なので関数化します。はい
    //では、まずは133から142まではコメントアウトしてしまいましょう。すみません。
    //いえいえ＾＾
    //あとはcatchが二重になっているので治します。

    // 1行ずつ結果を配列で取得します
    // $i = 0;
    // foreach ($rows as $row) {
    //   $data[$i]['item_id']   = htmlspecialchars($row['item_id'],   ENT_QUOTES, 'UTF-8');
    //   $data[$i]['name']      = htmlspecialchars($row['name'],      ENT_QUOTES, 'UTF-8');
    //   $data[$i]['price']     = htmlspecialchars($row['price'],     ENT_QUOTES, 'UTF-8');
    //   $data[$i]['img']       = htmlspecialchars($row['img'],       ENT_QUOTES, 'UTF-8');
    //   $data[$i]['status']    = htmlspecialchars($row['status'],    ENT_QUOTES, 'UTF-8');
    //   $data[$i]['stock']     = htmlspecialchars($row['stock'],     ENT_QUOTES, 'UTF-8');
    //   $i++;
    // }
    //ここはですね、こうしてしまえばオッケーです。ありがとうございます！
    //その上で、例外をスローするのは、内側のtry-catchから、外側のtry-catchへ例外を投げるためのものです。
    //一番外側のtry-catchでエラーをスローしてしまうと誰もcatchできなくなってしまうので、
    // スローはしません。なるほど、ちょっとこちらは後ほど復習しておきます！
    //はい、テキストの記述は少なめですので多分解説を聞かないとわかりません＾＾
    //さて、ではあとはdataの表示ですが、今回は$dataを使わなくなりました。
    //その上で、121行目を見てみましょう。
  } catch (PDOException $e) {
    // 例外をスロー
    // throw $e;
    echo 'データベース処理でエラーが発生しました。理由：'.$e->getMessage();
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
<?php foreach ($item_list as $value)  { ?>
<!--ひとまずこれで表示を確認してみましょう。51行目ですね！行きましょうはい-->
  <div class="item">
    <form action="itemlist.php" method="post">
      <span class="item_img_size"><img src="<?php print $img_dir . $value['img']; ?>"></span>
      <span><?php print $value['name']; ?></span>
      <span><?php print $value['price']; ?>円</span>
      <input type="hidden" name="sql_kind" value="add_product_to_cart">
<?php if ($value['stock'] > 0) { ?>
<!--はい、ここですね！ここではラジオボタンになっていますが、商品ごとに「カートに入れるボタン」があるので-->
<!--わざわざラジオボタンにする必要はありません。クリックするのが面倒なのでhiddenにしてしまいましょう。-->
<!--はい、これで、「カートに入れるボタン」を押すだけで、該当の商品が追加されるようにできます。もう一度ハンバーグのボタンを押してみましょう-->
<!--実行してみてください追加になっていないですかね。-->
<!--あ、それはまだです。ハンバーグのボタンを押したらハンバーグのidが送られるのでカートの中身がvar_dumpで表示され、-->
<!--まだカートに入っていない冷やし中華のボタンを押したらカートの中身がないのでからの配列が表示されませんか？はいその通りです！-->
<!--オッケーです！では上に戻りましょう８２行目です。はい-->
<input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
<?php 
} else {
?>
<span>売り切れ</span>
<?php } ?>
<input type="submit" value="カートに入れる">
     </form>
<?php } ?>
<?php foreach ($err_msg as $value) { ?>
     <p><?php print $value; ?></p>
<?php } ?>
  </div>
  </div>
</body>
</html>