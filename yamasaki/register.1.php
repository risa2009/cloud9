<?php
/* 最終課題の会員登録ページ */

// データベースの接続情報
//$dsn の代わりにDSNが使えるようになりました。
define('DB_USER',   'risayamasaki');    // MySQLのユーザ名
define('DB_PASSWD', '');    // MySQLのパスワード
define('DSN', 'mysql:dbname=camp;host=localhost;charset=utf8');  
   
$date = [];
$err_msg = [];  // エラーメッセージ用の配列
$result_msg = '';     // 実行結果のメッセージ
    
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  //こちら、69行目でエラー出てるので移動しましょう。
  $user_name = ''; //初期化
  $password = '';
          
  if (isset($_POST['user_name']) === TRUE) { //issetでのチェック
    $user_name = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['user_name']);  //全角と半角の空白を取り除く。受け取り
  }
  //ここからエラーチェック
  if($user_name === ''){ //未入力チェック//かっこを開いたら一つ右、かっこを閉じたら一つ左です。例外はありません。
    //ここはかっこを開いたので一つ右です。
    $err_msg[] = 'ユーザー名を入力してください。';
    //次でかっこを閉じるので一つ左です。
  }else if(preg_match('/^[a-z\d_]{6,20}$/i', '', $_POST['user_name'])){ //正規表現チェック
    //ここではい、そうですね＾＾
    $err_msg[] = "ユーザー名は半角英数字6文字以上でご入力ください。";
  }//オッケーです＾＾
    
  if (isset($_POST['password']) === TRUE) { //issetでのチェック
    $password = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['password']); //全角と半角の空白を取り除く。受け取り
  }
  //ここからエラーチェック
  if($password === ''){ //未入力チェック
    $err_msg[] = 'ユーザー名を入力してください。';
  }else if(preg_match('/^[a-z\d_]{6,20}$/i', '', $_POST['password'])){ //正規表現チェック
    $err_msg[] = "ユーザー名は半角英数字6文字以上でご入力ください。";
  }
}//かっこを閉じたら何も考えずに一つ左です。オッケーです＾＾
  
// DB接続前にcount($err_msg)をチェック
if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // データベースに接続
    //ここの$dsn, $username, $passwordがそれぞれ定数DSN, DB_USER, DB_PASSWDで置き換え
    $dbh = new PDO(DSN, DB_USER, DB_PASSWD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
     
    // 現在日時を取得
    $now_date = date('Y-m-d H:i:s');
     
    // select文で重複ユーザーをチェック
    $sql = 'SELECT
          user_name
          FROM users
          WHERE user_name = ?';
    
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    //SQLにプレスフォルダの値をバイント
    $stmt->bindValue(1, $user_name, PDO::PARAM_INT);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    //まずここの変数名を変えます。
    $user_list = $stmt->fetchAll();
    //  はい、ここですね！取得した結果のチェックがおかしくなっています。
    //先ほどと同じように考えると
    // select文の実行結果が１行以上あればエラーメッセージを表示
    //次に、ここでは$user_listの件数をチェックしますcount($user_list)をチェックしましょう！
    //はい、バッチリです＾＾
    //あとはすでにコメントしていらっしゃる部分ですね！
    if (count($user_list) >= 1) {
      $err_msg[] = 'ユーザ名がすでに登録されています。';
    }
    //インデントを直します。はい！そうですね！
    //はい、ここです。はい、こうです。こうして対応するかっこが探せます。
    //今回でいうとif文を閉じてないことがわかるわけです。わかりましたか？勘違いしていました。
    //if文を閉じてないことがわかる,,もう一度見ます。はい、では下のcatch節に行きましょう
    if(count($err_msg) === 0) {
      // エラーがなければユーザーをinsert
      try {
        $sql = 'INSERT INTO users (user_name, password, created_at)
                VALUES (?, ?, ?)';
        //はい、オッケーです。あとは、以前は$now_dateだったところが$created_atに
        //なってしまったようですね。オッケーです！こちらではい確認して見ましょう！
        //できました＾＾メッセージも出ました。
        //はい、バッチリですね＾＾では、改めて、登録したユーザーでログインして見ましょう！はい
        //ログインできませんでした。はい、そうですね＾＾ではloginに戻りましょう。はい
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $user_name,    PDO::PARAM_STR);
        $stmt->bindValue(2, $password,     PDO::PARAM_STR);
        $stmt->bindValue(3, $now_date,   PDO::PARAM_STR);
        
        // SQLを実行
        $stmt->execute();
        
        // 表示メッセージの設定
        $result_msg = 'アカウント作成が完了しました。<br>';
      } catch (PDOException $e) {
      // 例外をスロー
       throw $e;//はい、ここはかっこまでコメントアウトしてはいけませんね！
      }
      //ちょっとこの辺りで一旦チェックしましょう。はい
      //103行目のカッコとじの真上には何がありますか？かっこですね
      //真上とはインデントが同じ高さの開くかっこです。ifを閉じてしまうという意味ですね
      // はい、そういうことになります。もう一度上に行くのをやって見ましょう。カーソルについてきてください。
      //はい、ここですね。catch節が連続2回続いているので、ちょっとおかしいなと違和感を持ちます。
      //そして、このcatch節はどこのtry節と対応しているのだろう？と上に登って行くわけです。はい！
      //では、今度は山崎さんが登って行って見ましょう！わかりました！＾＾
      // では、if文を閉じてあげましょう！
    }//オッケーです＾＾続きをインデントして行きましょう。
  } catch (PDOException $e) {//一つだけです。オッケーです。
    $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。'.$e->getMessage();
  }//スペース使わず、tabを使いましょう。オッケーです＾＾
}
//はい、これが余計ですね！オッケーです＾＾では確認して見ましょう！
//ちょっとresult_msgの表示部分がないみたいですね！
//result_msgはhtml内で表示してください。はい
//$result_msgをhtml内でprintして見ていただけますか？
var_dump($err_msg);//仮
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>My Apron：無料会員登録</title>
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
      <p><?php print($result_msg); ?></p>
      <form method="post" action="register.php">
        <div>ユーザー名：<input type="text" name="user_name" placeholder="ユーザー名"></div>
        <div>パスワード：<input type="password" name="password" placeholder="パスワード"></div>
        <div><input type="submit" value="新規登録"></div>
        <!--単にphpでprintしていただくだけで大丈夫ですよ！-->
        <!--はい、オッケーです＾＾一応こうして、formの外には出しておきましょうか。-->
        <!--こちらでチェックして見ましょう！-->
        <!--登録ができないです。先ほどの正規表現部分、ここからとってきたのが違いましたので修正します-->
        <!--ちょっと待ってくださいね。はい87行目に全角があるようです。削除しましょう！-->
      </form>
    </div>
  </div>
</body>
</html>