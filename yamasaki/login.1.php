<?php
/* 最終課題のログインページ */

// データベースの接続情報
//$dsn の代わりにDSNが使えるようになりました。
define('DB_USER',   'risayamasaki');    // MySQLのユーザ名
define('DB_PASSWD', '');    // MySQLのパスワード
define('DSN', 'mysql:dbname=camp;host=localhost;charset=utf8');

$err_msg = [];  // エラーメッセージ用の配列
$msg      = [];     //エラー以外のメッセージを格納する配列
$user_name = ''; //初期化
$password = '';

//まず、$_POST見て見ます。
var_dump($_POST);

// リクエストメソッド確認
//実は、こちら、MVCモデルでコントローラーを分離した時用の記述となります。そうだったのですね。
//GETでアクセスされることがないファイルなので、この処理が行われていました。
//ですのでここはまとめてコメントアウトでオッケーです。
// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//   // POSTでなければログインページへリダイレクト
//   //オッケーです！^^
//   //あとはエラーがなければログインチェックの処理なのでtry文の手前に行きましょう！
//   header('Location: login.php');
//   exit;
// }
//セッション開始
session_start();

//まず、ここはログイン済みかどうかのチェックです。これは最初に行うことになりますね！
// セッション変数からログイン済みか確認
if (isset($_SESSION['user_id'])) {
  // ログイン済みの場合、ホームページへリダイレクト
  header('Location: itemlist.php');
  exit;
}

// POST値取得
//はい、こちらですね＾＾get_post_dataは作ってないので普通にissetでチェックして受け取りです。

//ログインボタンが押された場合
//まず、ここを見ていただくと、本来はここが受け取り処理です。
//ただ、なぜかチェックしている値だけおかしいです。
if (isset($_POST['user_name']) === TRUE){
  //こっちは置換処理なのでpreg_replaceが必要です。
  $user_name = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['user_name']);
}
//ユーザー名エラーチェック
//ここなのですが、先ほどのqa_productionで松尾さんの質問でここにお答えしています。
//また、お気付きのようですが、$_POST['user_name']ではなく$user_nameをチェックすべきですね。
//ここではpreg_matchとpreg_replaceの混同が起こっています。
//preg_replaceはpreg_replace(正規表現, マッチした部分を置き換える文字, 置き換える文字列)です。
//preg_matchは(正規表現, チェックする文字列)です。preg_matchの二つ目の引数が空文字になるのは間違いです。
//そうですね！そして、$_POST['user_name']ではなく$user_nameをチェックすべきです
//元の$_POST['user_name']が$user_nameに入れ替わることになりますね！
//   $err_msg[] = "ユーザー名は半角英数字6文字以上でご入力ください。";
// }
//元はこうでしたのでこうするということになりますね！すみません。
//いえいえ＾＾パスワードも同様です。こちらオッケーです！
if($user_name === ''){ 
  $err_msg[] = 'ユーザー名を入力してください。';
}else if(preg_match('/^[a-z\d_]{6,20}$/i', $user_name) !== 1){ 
  $err_msg[] = "ユーザー名は半角英数字6文字以上でご入力ください。";
}
  
if (isset($_POST['password']) === TRUE) {
  $password = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['password']);
}    
//パスワードエラーチェック
if($password === ''){ 
  $err_msg[] = 'パスワードを入力してください。';
}else if(preg_match('/^[a-z\d_]{6,20}$/i', $password) !== 1){
  $err_msg[] = "パスワードは半角英数字6文字以上でご入力ください。";
}
//はい、ここですでにエラーが出ていればDBをチェックするまでもないので
//ここまででエラーがなければ、というif文でtry-catch節全体をくくってあげましょう！
var_dump($err_msg);
if(count($err_msg) ===0) {

  //この高さの真下で初めて出てきた閉じかっこが対応する閉じです。
  try {
    // データベースに接続
    //ここの$dsn, $username, $passwordが定数DSN, DB_USER, DB_PASSWDで置き換え
    $dbh = new PDO(DSN, DB_USER, DB_PASSWD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //今回は、user_nameだけでなく、「ユーザー名とパスワードが両方一致したら」ですね。
    //そして、ユーザー名もpasswordも特に取得する必要はありませんね！
    //今回取得するのは「ユーザー名とパスワードが両方一致するユーザー」の何でしょうか？idでしょうか
    //はい、そうですね！ですので「ユーザー名とパスワードが両方一致するユーザーのidを取得」するselect文にしましょう！
    //今、何のための処理を書いていらっしゃいますか？両方一致するユーザーをチェックするための処理を書こうと思ったのですが、、
    //なるほど、今回書いていただきたいのはselect文です。
    //SQLで、ユーザー名とパスワードが両方一致するユーザーのidを取得するにはどうしますか？
    //例えば、ユーザー名がabcでパスワードがpassのユーザーのidを取得するのはどんなselect文ですか？
    //ちょっとselect文の文法を確認しましょう。
    // SELECT 取得したいカラム FROM 取得元の表 WHERE 条件 となります。
    //少なくともabc とpassは取得したいカラムではありませんね。これらは値です。
    //取得したいカラムは何ですか？user_name
    //なるほど、もう一度確認して見ましょう。今回書いていただきたいのはごめんなさいIDでした。
    //はい、そうですね！オッケーです＾＾ちなみに、WHERE文で「かつ」の条件はどう表しますか？
    // ANDでしょうか？はい、そうです＾＾オッケーです！あとは、abcやpassと入ってしまっているのを
    //プレースホルダにかえるだけですね！オッケーです！
    //では、バインドもしてあげましょう！
    //はい、これでもし該当の人がいればidを取得できることになります。
    //重複ユーザーも同じような形でチェックできそうです＾＾
    $sql = 'SELECT 
              id
              FROM  users
              WHERE user_name = ? AND password = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $user_name, PDO::PARAM_INT);
    $stmt->bindValue(2, $password,  PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    var_dump($rows);
    //では、ここで一致するユーザーのidが取得できたかどうか確認しましょう。
    //このようにするかこのどちらかで確認できます。今回はidを取得するので
    // if(count($rows) >=1){ //1件以上取得できたら
      
    // }
    //こちらが良いでしょう。
    //ではidが取得できたとして、セッションにuser_idを保存して見ましょう。単に代入するだけです。
    //はい、オッケーです！あとは取得できなかった場合ですね！
    if(isset($rows[0]['id']) === true){ //idが取得できていたら
      //セッションにuser_idを保存する処理
      //あとは、ログインできた時にはホーム画面にリダイレクトしてあげる必要がありますね！
      //132行目からを参考にしましょう！
      $_SESSION['user_id'] = $rows[0]['id'];
      //あ、ここで大丈夫です。ここがログインに成功した場合の分岐ですね！
      //もう成功しているのでif文はいりませんよ！単にリダイレクトするだけで、何の条件も不要です。
      //はい、これでリダイレクトですね！リダイレクト先はどこですか？オッケーです！
      //では残りの処理を見て行きましょう134からですね。
      header('Location: itemlist.php');
      exit;
    }else{
      //シンプルにかっこを開いたら次は一つ右、かっこを閉じたら一つ左です。
      //user_idを取得できなかった場合（失敗メッセージ）
      $err_msg[] = 'ログインできませんでした。';
    }
  }catch (PDOException $e) {
    echo 'データベース処理でエラーが発生しました。理由：'.$e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>My Apron：ログインページ</title>
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
  <div class="contents">
    <div class="register">
      <form method="post" action="login.php">
        <div>ユーザー名：<input type="text" name="user_name" placeholder="ユーザー名"></div>
        <div>パスワード：<input type="password" name="password" placeholder="パスワード"></div>
        <div><input type="submit" value="ログイン"></div>
      </form>
    </div>
    <a href="register.html">ユーザーの新規作成</a>
  </div>
</body>
</html>