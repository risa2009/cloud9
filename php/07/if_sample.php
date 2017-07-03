<!DOCTYPE html>
<html lang="ja">
<head>
  <title></title>
  <meta charset="utf-8">
</head>
<body>
  <?php
  $rand = mt_rand(1, 10); // １〜１０の値をランダムに取得
  ?>
  <!DOCTYPE html>
  <head>
    <meta charset="UTF-8">
    <title>ifの使用例</title>
  </head>
  <body>
    <p>抽選システム</p>
    <p>値は：<?php print $rand; ?></p>
    <?php if ($rand <=3) { ?>
    <p>当たり！！</p>
    <?php } else { ?>
    <p>残念でした‥また引いてね</p>
    <?php } ?>
</body>
</html>
