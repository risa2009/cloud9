<?php
$filename = './review.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $comment = $_POST['comment']; 
    $date = date("-Y-m-d H:i:s");
    $User_name = $_POST['name'];
    
    
    
    if (($fp = fopen($filename, 'a')) !== FALSE) {
      if (fwrite($fp, $User_name . "\t" . $comment . "\t" . $date . "\t\n") === FALSE) {
          print 'ファイル書き込み失敗: ' . $filename;
      }
      fclose($fp);
    }
}

$data = [];

if (is_readable($filename) === TRUE) {
 if(($fp = fopen($filename, 'r')) !== FALSE) {
    while (($tmp = fgets($fp)) !== FALSE) {
        $data[] = htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8');
    }
    fclose($fp);
 }
} else {
    $data[] = 'ファイルがありません';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
 <meta charset="UTF-8">
 <title>ひとこと掲示板</title>
</head>
<body>
  <h1>ひとこと掲示板</h1>
  <form method="post">
      <label>名前: </label><input type="text" name="name">
      <label>ひとこと: </label><input type="text" name="comment">
      <input type="submit" value="送信">
  </form>
  <ul>
    <?php foreach ($data as $read) { ?>
    <li>
    <?php print $read; ?>
    </li>
  </ul>
<?php } ?>
</body>
</html>