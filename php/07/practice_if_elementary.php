<!DOCTYPE html>
<html lang="ja">
<head>
  <title></title>
  <meta charset="utf-8">
</head>
<body>
  <?php
  // 0〜2のランダムな数値を2つ取得し、それぞれ変数$rand1と$rand2へ代入
  $rand1 = mt_rand(0, 2);
  $rand2 = mt_rand(0, 2);
  
  // ランダムな数値$rand1と$rand2をそれぞれ表示
  print 'rand1: ' . $rand1 . "\n";
  print 'rand2: ' . $rand2 . "\n";
  
  // $rand1と$rand2のどちらのほうが大きいか比較し、結果を表示
  // $rabd1の方が大きい場合
  if ($rand1 >= $rand2) {
     print 'rand1の方が大きい' . "\n";
  } else if ($rand1 <= $rand2) {
     print 'rand2の方が大きい' . "\n";
  // 同じ場合
  } else if ($rans1 === $rabd2) {
    print '同じです' . "\n";
  }
  ?>
</body>
</html>