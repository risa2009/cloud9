<?php
$sum = 0;

$i = 0;
while ($i <= 100) {
    if ($i % 3 === 0) {
     $sum = $sum + $i;
     }
      $i++;
}
print '合計: ' . $sum;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>繰り返し課題1　while文</title>
</head>
<body>
</body>
</html>