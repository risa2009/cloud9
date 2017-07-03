<?php
$user_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (isset($_POST['user_name']) === TRUE) {
    $user_name = trim($_POST['user_name']);
    $user_name = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>課題</title>
</head>
<body>
<?php if ($user_name !== '') { ?>
  <p>ようこそ<?php print $user_name; ?>さん</p>
<?php } else { ?>
  <p>名前を入力してください</p>
<?php }  ?>
</body>
</html>
