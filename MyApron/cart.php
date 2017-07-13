<?php
/* 最終課題のカートページ */
 $host     = 'localhost';
 $username = 'risayamasaki';   // MySQLのユーザ名
 $password = '';       // MySQLのパスワード
 $dbname   = 'camp';   // MySQLのDB名
 $charset  = 'utf8';   // データベースの文字コード
 
  // MySQL用のDNS文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$img_dir    = './img/';  // 画像のディレクトリ

$sql_kind   = '';     // SQL処理の種類
$data       = [];     // DBから取得した値を格納する配列
$err_msg    = [];     // エラーメッセージを格納する配列

$item_id    = '';

if (isset($_POST['item_id']) === TRUE) {
    $item_id = $_POST['item_id'];
  }

if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // はい、ここですね。
    // 現在日時を取得
    $now_date = date('Y-m-d H:i:s');
//まずはここがおかしいです。if節の中なので一つ右ですね。
    try {
        // データベースに接続
        $dbh = new PDO($dsn, $username, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        
        // カート内チェック
        //ここで使っている、sql_kindという変数は何のためのものですか？
        //sqlの処理で使うためのものだと思っているのですが、きちんとわかっていないです
        //ありがとうございます。こちらはまずPOSTから受け取った値だということはわかりますか？
        //ありがとうございます。つまり、この変数は「フォームのどのボタンを押したかによって処理を切り替えるための変数」です。
        //例えば、自動販売機の管理画面であれば、商品追加のボタンを押したのか、
        //在庫数更新のボタンを押したのか、ステータス変更のボタンを押したのかで、処理を切り替えるためのものです。
        //ありがとうございます！そして、今回はSELECTからINSERT or UPDATEという処理になりますが、
        //これはどのボタンを押しても共通の内容です。つまり、ボタンによって処理を切り替える必要はなく、
        //単にitem_idの値が変化すれば良いだけです。従って、今回のプログラムでは
        //$sql_kindは不要になり、同時にこのif文も不要ということになります。
        //よくわかりました！前のページでカートに入れて、どのタイミングでカートテーブルに商品が入るのか？そこがちゃんとわからなかったので、
        //確かにボタンによって処理を切り替える必要はないですよね。
        //はい、そうなりますね！また、今回の場合はcart.phpにカートへの追加の処理を書いていらっしゃいますが、
        //サンプルのECサイトでは商品一覧のページでカートへの追加を行なっているのは確認しましたか？
        //なるほど、では一度確認しておいてください。一覧のページでカートに追加して、カートのページでは追加した商品の一覧表示をしています。
        //カートに追加した時点では、商品一覧のページのままです。ですので、今書いていただいている処理は
        //サンプルと同じ動作ならばitemlist.phpで行うことになりますね！
        //はい！お願いします！本日はサポートタイム後すぐに次の仕事があるので、最後まで確認できないのが残念ですが、
        //まずはサンプルを参考に処理を確認して見てくださいね！
        //はい、よろしくお願いします！明日もどんどん質問してくださいね！
        //はい＾＾では失礼します！＾＾
            $sql = 'SELECT
                       carts.user_id,
                       carts.item_id,
                       carts.amount
                    FROM carts
                    WHERE item_id = ?
                      AND user_id = ?';
                
            // SQL文を実行する準備
            $stmt = $dbh->prepare($sql);
            // SQL文のプレースホルダに値をバインド
            $stmt->bindValue(1, $user_id,    PDO::PARAM_INT);
            $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
            $stmt->bindValue(3, $amount,     PDO::PARAM_INT);
            $stmt->bindValue(4, $now_date,   PDO::PARAM_STR);
            
            // SQLを実行
            $stmt->execute();
            //はい、オッケーです。そうしたら、今度は41行目に行きましょう。
            // そうですね！try節はまだ続くので、閉じかっこも不要です。
            // すると、まずここの閉じかっこがPOSTチェックの閉じになっていますね。何かおかしいです。
            // try文の中でifが始まっているのに、try文の中でifが閉じていません。
            // こちらはまだtry文を閉じる必要はなさそうですね！
            //あ、ごめんなさい、私の書き込みの内容は見えていますか？はい、みています！
            // ありがとうございます！では61行目でtry文を閉じているのがおかしいのはわかりますか？
            //すみません、ちょっとわからなかったので、今かっこの確認していました。
            //ありがとうございます。41行目でif文を開いているのはわかりますか？はい、先ほど足した部分ですね
            //そうですね！ところが、そもそも2つのtry文をまたいでif~else ifの構文を使うことはできません。
            //どのかっこが対応しているのかわからなくなってしまいます。
            //また、そもそもこの時点でcatchする必要がありません。そうなのですね。try-catchと思っていて都度つけていました。
            //なるほど。try-catchはtry節の中で例外が発生した時に、途中の処理を飛ばしてcatch節にジャンプするための構文です。
            //今回の場合は、select文で例外が発生したらそのまま残りの処理も必要ないので全部の処理が終わったところで
            //catchすればオッケーです。selectで例外が発生したら、insertやupdateはスキップすることになります。よくわかりました！ありがとうございます、
            // では61行目のcatch節は消してしまいましょう。

    // 2-a. 1のSELECTで合致する行がある場合amountをUPDATE（購入数+1)
} else if ($sql_kind  === 'update') {
    //ちょっとインデントが崩れて全体的に構造がわかりにくくなっていらっしゃいますね。
    //急がば回れですので一旦直してしまいましょう。
    // まずは30行目あたりから見ていきましょう。
  try {
    $sql = 'update carts SET amount = ? + 1';
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $amount,    PDO::PARAM_INT);
    
    // SQLを実行
    $stmt->execute();
  }catch (PDOException $e) {
   // 例外をスロー
   throw $e;
  }
  
   // 2-b. 1のSELECTで合致する行がなければ、カートにINSERT
} else {
  try {
    $sql = 'INSERT INTO carts (user_id, item_id, amount, create_datetime) 
            VALUES (?, ?, ?, ?)';
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $user_id,    PDO::PARAM_INT);
    $stmt->bindValue(2, $item_id,    PDO::PARAM_INT);
    $stmt->bindValue(3, $amount,     PDO::PARAM_INT);
    $stmt->bindValue(4, $now_date,   PDO::PARAM_STR);
    
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();
    
    // 1行ずつ結果を配列で取得します
    $i = 0;
    foreach ($rows as $row) {
     $data[$i]['user_id']   = htmlspecialchars($row['user_id'],   ENT_QUOTES, 'UTF-8');
     $data[$i]['item_id']   = htmlspecialchars($row['item_id'],   ENT_QUOTES, 'UTF-8');
     $data[$i]['amount']    = htmlspecialchars($row['amount'],    ENT_QUOTES, 'UTF-8');
     $i++;
    }
  } catch (PDOException $e) {
    // 例外をスロー 
    throw $e;
  }
} 
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>My Apron：カート</title>
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
  <div class="cart_list">
  <div class="cart">
  <table>
   <tr><th>商品名</th><th>単価</th><th>数量</th><th>小計</th></tr>
   <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
   </tr>
   <tr><td colspan='2'> </td><td><strong>合計</strong></td><td>円</td></tr>
  </table>
  </div>
  </div>
</body>
</html>