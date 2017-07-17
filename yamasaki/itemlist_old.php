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
    
      //itemlistのページは
      // 1. POSTされていればカートへの追加処理
    // // 2. POSTされていてもいなくても一覧表示
    // // 大枠で見るとこのようになります。はい
    // //ですので
    // // try{
    // //   DB接続
    // //   if($[REQUEST_METHOD] === 'POST'){
    // //     カート追加処理
    // //   }
    // //   一覧表示処理
    // // }catch(PDOException e){
    // //   print $e->getMessage();
    // // }
    
    //まずはここでPOSTの場合の処理となります。if文を追加してみましょう！
    //オッケーです！ではこちらでSELECTからのINSERT or UPDATEの処理を行いましょう。
    //はい、まずはSELECTの部分ですね！ここではSELECT文を実行してデータを取得します。
    //ですので、executeした後にfetchAllが必要になります。付け加えてみましょう！SELECT後につけてみました！
    //ありがとうございます！では、まずこの取得したデータは、carts内のデータなので
    //名前は$rowsではなく$cart_listにしておきましょう。はい変更しました
    
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
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
      //ここでデータベースに一部虫食いのSQL文をプリペア（準備）
      //そして、このデータベースに準備されたSQLをプリペアドステートメント(Prepared Statement)と言います。
      //$stmtはstatementを表す略字で、まさにこのプリペアドステートメントを表しています。
      $stmt = $dbh->prepare($sql);
      
      // SQL文のプレースホルダに値をバインド
      $stmt->bindValue(1, $item_id,    PDO::PARAM_INT);
      $stmt->bindValue(2, $user_id,    PDO::PARAM_INT);
  
      // SQLを実行
      $stmt->execute();
      
      // レコードの取得
      $cart_list = $stmt->fetchAll();
      //はい、ここで$cart_listを取得したので中身を確かめましょう。
      //var_dump($cart_list);

      //カートの中身があるかないかで分岐させて行きましょう。はい
      //単純なif文です。これで、カートが取得できて入れば行数が1より大きくなるので
      //たったこれだけで、cartに中身があるかないかは判定できます。

      if(count($cart_list) > 1){
        //カートに入っているのでupdate
        //まずはインデントの修正です。かっこの中身は一つインデントなので
        
        $sql = 'UPDATE carts
          SET amount = ?
          WHERE item_id = ?';
         
        //ここのamountですが、まずsqlの中で計算するよりも、計算してからbindValueしてしまう方が良いですね。
        //その上で、$amountの値は、$cart_listで取得した値に含まれています。
        //今回、$cart_listは今後の動作が正しくなって入れば、特定のユーザーの特定の商品のカートデータは
        //1件だけになります。
        //ですので$cart_listの0番目に該当のカートデータが入っていて、そこのamountの値を取得すればオッケーです。
        //つまり$cart_list[0]['amount']に欲しい値が入っています。ここまではいかがですか？
        //では、今回設定する$amountの値はどう計算できますか？
        //まず、$cart_list[0]['amount']に入っている数字は、「現在カートに入れている購入数」です。
        //では、カートに5個入っている状態で「カートに入れるボタン」を押したら、カートにいくつ入れたことになって欲しいですか？6こ
        //そうですね！そして$cart_list[0]['amount']には5という数字が入っています。そうしたらどうすればいいですか？+1をしたいです
        //そうですね！しましょう！なるほど、これだと$cart_listの1番目になってしまいますね。
        //そうではなく、$cart_list[0]['amount']に1を足しましょう。
        //これだと$cart_list[0]['amount1']を取得することになりますね。
        //そうではなく$cart_list[0]['amount']が5なのです。
        //これで、正しく今回設定したい$amountの数字が計算できました。
        
        $amount = $cart_list[0]['amount'] +1;
        
        //はい、ここでエラーが出ていますね！理由はわかりますか？なぜでしょうか？
        //エラーメッセージを読みましょう！なんと書いてありますか？
        //Parse error: syntax error, unexpected '$stmt' (T_VARIABLE) です。$stmtの構文エラーとなっています。
        //はい、そうですね！unexpectedつまり、予想外の$stmtが出てきたと。この$stmtってなんですか？
        //はい！そうですね！虫食いのSQL文をプリペアして、虫食いの部分に$amountがバインドされています。

        // データベース処理でエラーが発生しました。理由：SQLSTATE[HY093]: Invalid parameter numberと出ています。
        // Invalid parameter numberとは、バインドする変数の数がおかしいということです。

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $amount,     PDO::PARAM_INT);
        
        //そして、今回最も大事なのは、エラー修正のプロセスと、制作のプロセスです。
        //まず、制作に関してはまず大枠の流れを決めて、そこから一つ一つの処理を書いていくこと。
        //エラー修正に関しては、エラーメッセージの内容を読む前に修正内容を決めないことです。
        //エラーメッセージが読めない→おかしそうなところを修正してみる、ではなく
        //エラーメッセージが読めない→検索or質問でエラーメッセージの内容を理解する
        //→問題の原因を突き止める→原因を取り除く
        //という手順にしましょう。怪しいところを当てずっぽうで修正すると、治っても理由がわからないままになります。
        //慣れるとすぐにできますし、質問すれば一瞬で答えてもらえる今がチャンスです！

        $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
        $stmt->execute();
        // update文では、結果を取得するわけではないのでfetchallは不要です。はい
      
      }else{
        //カートに入っていないので、insert
        //今回、なぜUPDATEではなくINSERTするんでしたっけ？
        //カートに入っていないので、insertでしたね。$amountを1としたいです

        $amount = 1;//オッケーです！
        
        //ちなみに今回はこれでオッケーですがもう一つ$stmt->bindValue(3, 1, PDO::PARAM_INT);としても良いです。

        $sql =  'INSERT INTO carts (user_id, item_id, amount, create_datetime) 
                VALUES (?, ?, ?, ?)';
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $user_id,    PDO::PARAM_INT);
        $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
        $stmt->bindValue(3, $amount,     PDO::PARAM_INT);
        $stmt->bindValue(4, $now_date,   PDO::PARAM_STR);
        
        $stmt->execute();
      }

        
    }//ここがPOSTの閉じですね。

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
    //表示部分ですので、htmlの中のforeachに行きましょう！

    //まず、ここで一覧表示用のデータが$rowsに代入されています。はい
    //そうですね！では、ここの$rowsから$dataに移す処理ですが、実は不要です。
    //$rows自体がすでに結果の配列になっているのであとは出力時にhtmlspecialcharsをかけるだけで良いのです。
    //ただ、htmlspecialcharsも毎回かくと面倒なので関数化します。
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
    //ここはですね、こうしてしまえばオッケーです。例外を投げるためのものです。
    //一番外側のtry-catchでエラーをスローしてしまうと誰もcatchできなくなってしまうので、
    // スローはしません。なるほど、ちょっとこちらは後ほど復習しておきます！
    //はい、テキストの記述は少なめですので多分解説を聞かないとわかりません＾＾
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
<!--ここではラジオボタンになっていますが、商品ごとに「カートに入れるボタン」があるので-->
<!--わざわざラジオボタンにする必要はありません。クリックするのが面倒なのでhiddenにしてしまいましょう。-->
<!--はい、これで、「カートに入れるボタン」を押すだけで、該当の商品が追加されるようにできます。-->
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