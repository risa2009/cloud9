<?php
$sum = 0;

for ($i=1; $i<=100; $i++) {
    if($i % 3 === 0) {
        $sum += $i;
    }
}

print '合計: ' . $sum;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>課題</title>
</head>
<body>
</body>
</html>