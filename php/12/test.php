<?php
$filename = './test.txt';
$comment = '';
$name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $post_n = $_POST['name'];
  $post_c = $_POST['comment'];
  
  if ($post_n && $post_c && strlen($post_n) <= 20 && strlen($post_c) <= 100) {
  $comment = $post_n."\t".$post_c."\t".date('-Y-m-d H:i:s')."\n";
} else if ($post_c === '0' && $post_n && strlen($post_n) <= 20) {
  $comment = $post_n."\t".$post_c."\t".date('-Y-m-d H:i:s')."\n";
} else if ($post_n === '0' && $post_c && strlen($post_c) <= 100) {
  $comment = $post_n."\t".$post_c."\t".date('-Y-m-d H:i:s')."\n";
} else if ($post_n === '0' && $post_c === '0'){
  $comment = $post_n."\t".$post_c."\t".date('-Y-m-d H:i:s')."\n";
}
  if (($fp = fopen($filename, 'a')) !== FALSE) {
    if (fwrite($fp, $comment) === FALSE) {
      print 'ファイル書き込み失敗:  ' . $filename;
    }
    fclose($fp);
  }
}

$data = [];

if (is_readable($filename) === TRUE) {
  if (($fp = fopen($filename, 'r')) !== FALSE) {
    while (($tmp = fgets($fp)) !== FALSE) {
      $data[] = htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8');
    }
    fclose($fp);
    } else {
  $data[] = 'ファイルがありません';
}
}

?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>ひとこと掲示板</title>
  </head>
  <body>
    <h1>ひとこと掲示板</h1>
    <ul>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $post_n = $_POST['name'];
            $post_c = $_POST['comment'];
              if (empty($post_n) && $post_n !== '0') { ?>
      <li><?php print '名前を入力してください'.'<br>'; ?></li>
      <?php } else if (mb_strlen($post_n) >= 20 ) {?>
      <li><?php print '名前は20文字以内で入力してください'.'<br>'; ?></li>
      <?php }  ?>
      <?php if (empty($post_c) && $post_c !== '0') { ?>
      <li><?php print 'ひとことを入力してください'.'<br>'; ?></li>
      <?php } else if (mb_strlen($post_c) >= 100 ) { ?>
      <li><?php print 'ひとことは100文字以内で入力してください'.'<br>'; ?></li>
      <?php } } ?>
    </ul>
    <form method="post">
      <label>名前:</label>
      <input type="text" name="name" size="21">
      <label>ひとこと:</label>
      <input type="text" name="comment" size="57">
      <input type="submit" value="送信">
    </form>
    <ul>
      <?php foreach ($data as $text) { ?>
      <li>
      <?php print $text; ?>
      </li>
      <?php } ?>
    </ul>
  </body>
</html>