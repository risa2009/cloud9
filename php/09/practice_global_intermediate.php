<?php
// 変数初期化
$result      = '';
$user_choice = '';
$cp_choice   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (isset($_POST['user_choice']) === TRUE) {
    $user_choice = $_POST['user_choice'];
  }

  if ($user_choice === '') {
    $user_choice = '未選択';
  }

  $rand = mt_rand(0, 2);

  if ($rand === 0) {
    $cp_choice = 'グー';
  } else if ($rand === 1) {
    $cp_choice = 'チョキ';
  } else if ($rand === 2) {
    $cp_choice = 'パー';
  }

  if ($user_choice === 'グー') {

    if ($cp_choice === 'チョキ') {
      $result = 'Win!!';
    } else if ($cp_choice === 'パー') {
      $result = 'lose...';
    } else if ($cp_choice === 'グー') {
      $result = 'draw';
    }

  } else if ($user_choice === 'チョキ') {

    if ($cp_choice === 'パー') {
      $result = 'Win!!';
    } else if ($cp_choice === 'グー') {
      $result = 'lose...';
    } else if ($cp_choice === 'チョキ') {
      $result = 'draw';
    }

  } else if ($user_choice === 'パー') {

    if ($cp_choice === 'グー') {
      $result = 'Win!!';
    } else if ($cp_choice === 'チョキ') {
      $result = 'lose...';
    } else if ($cp_choice === 'パー') {
      $result = 'draw';
    }

  } else {
    $result = '';
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
    <article>
        <h1>じゃんけん勝負</h1>
        <section>
            <p>自分: <?php print $user_choice; ?></p>
            <p>相手: <?php print $cp_choice; ?></p>
            <p>結果: <?php print $result; ?></p>
        </section>
        <form method="post">
            <p>
                <input type="radio" name="user_choice" value="グー" <?php if ($user_choice === 'グー') { print 'checked'; } ?>>グー
                <input type="radio" name="user_choice" value="チョキ" <?php if ($user_choice === 'チョキ') { print 'checked'; } ?>>チョキ
                <input type="radio" name="user_choice" value="パー" <?php if ($user_choice === 'パー') { print 'checked'; } ?>>パー
            </p>
            <button type="submit">勝負!!</button>
        </form>
    </article>
</body>
</html>
