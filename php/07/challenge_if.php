<!DOCTYPE html>
<html lang="ja">
<head>
  <title></title>
  <meta charset="utf-8">
</head>
<body>
　<?php
  $rand = mt_rand(1, 6); // １〜６の値をランダムに取得
  ?>
　<!DOCTYPE html>
　<head>
　  <meta charset="utf-8">
　  <title>条件分岐課題１</title>
　</head>
　<body>
　  <p>出た数字：<?php print $rand; ?></p>
　 <?php if ($rand % 2 === 0) { ?>
　  <p>偶数</p>
　 <?php } else { ?>
　 <p>奇数</p>
　 <?php } ?>
</body>
</html>