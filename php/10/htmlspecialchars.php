<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>htmlspecialchars</title>
  </head>
  <body>
    <?php
    // htmlを入力
    $str = '<h2>遊びに行きたい！</h2>';
    
    // htmlspecialcharsを使わない場合
    print $str;
    
    // htmlspecialcharsを使う場合
    print htmlspecialchars($str, ENT_QUOTES);
    ?>
  </body>
</html>