<?php

$filename = 'tokyo.csv';

$data = [];

if (is_readable($filename) === TRUE) {
    if (($fp = fopen($filename, 'r')) !== FALSE) {
    
    while(($tmp = fgetcsv($fp, 1000, ",")) !== FALSE) {
        $data[] = $tmp;
    }
    fclose($fp);
    }
    
} else {
    $data[] = 'ファイルがありません';
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<title>課題2</title>
<style>
    table {
        border-collapse: collapse;
    }
    table, tr, th, td {
        border: solid 1px;
    }
    caption {
        text-align: left;
    }
</style>
</head>
<body>
  <p>以下にファイルから読み込んだ住所データを表示</p>
<table>
    <caption>住所データ</caption>
　<tr><th>郵便番号</th><th>都道府県</th><th>市町村</th><th>町域</th>
　</tr>
　<?php foreach ($data as $value) { ?>
　<tr>
　    <td><?php print htmlspecialchars($value[2], ENT_QUOTES, 'UTF-8'); ?></td>
　    <td><?php print htmlspecialchars($value[6], ENT_QUOTES, 'UTF-8'); ?></td>
　    <td><?php print htmlspecialchars($value[7], ENT_QUOTES, 'UTF-8'); ?></td>
　    <td><?php print htmlspecialchars($value[8], ENT_QUOTES, 'UTF-8'); ?></td>
　</tr>
　<?php } ?>
</table>
</body>
</html>