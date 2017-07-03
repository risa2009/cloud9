<?php

 // 1から100までのループ
for ($i = 1; $i<=100; $i++) {
 // 3で割り切れる場合
 if ($i % 3 === 0) {
     print 'Fizz' . "\n";
 // 5で割り切れる場合
 } else if ($i % 5 === 0) {
     print 'Buzz' . "\n";
 // 3でも5でも割り切れる場合
 } else if ($i % 3 === 0 || $i % 5 === 0) {
     print 'FizzBuzz' . "\n";
 // 条件外
 } else {
     print $i . "\n";
 }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>繰り返し課題3</title>
</head>
<body>
</body>
</html>