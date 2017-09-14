 <!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.O">
  <link rel="stylesheet" href="./css/html5reset-1.6.1.css">
  <link rel="stylesheet" href="./css/MyApron_item.css">
  <link rel="stylesheet" href="./css/font-awesome.min.css">
  <title>My Apron | ショッピングカート</title>
</head>
<body>
    <table>
      <tr>
        <th>ご注文商品</th>
        <th>価格(税込)</th>
        <th>注文数</th>
        <th>小計</th>
      </tr>
<?php foreach ($cart_list as $cart_item)  { ?>
      <tr>
        <td>
          <table class="item-table">
            <tr>
              <td>
                <img class="cart-item-img" src="<?php print h($img_dir . $cart_item['img']); ?>">
              </td>
              <td>
                <span class="cart-item-name"><?php print h($cart_item['name']); ?></span>
              </td>
            </tr>
          </table>
          </td>
          <td>
            <span class="cart-item-price"><?php print h($cart_item['price']); ?>円</span>
          </td>
          <td>
            <span class="visible-phone">注文数：</span>
            <form class="form_select_amount" method="post">
              <input type="text" class="input_text_width text_align_right" name="change_amount" value="<?php print h($cart_item['amount']); ?>">個&nbsp;&nbsp;<input type="submit" value="変更">
              <input type="hidden" name="item_id" value="<?php print h($cart_item['item_id']); ?>">
              <input type="hidden" name="sql_kind" value="change_amount">
            </form>
          </td>
          <td class="total-itemprice-part">
            <span class="buy-sum-title">小計：</span>
            <span class="buy-sum-price"><?php print h($total); ?>円</span>
            <form class="cart-item-del" method="post">
              <input type="submit" value="削除">
              <input type="hidden" name="item_id" value="<?php print h($cart_item['item_id']); ?>">
            　<input type="hidden" name="sql_kind" value="delete_cart_item">
            </form>
<?php } ?>
          </td>
        </tr>
      </table>

    
    <div class="buy-sum-box">
       <span class="buy-sum-title">合計:</span>
       <span class="buy-sum-price"><?php print h($total); ?>円</span>
    </div>

</body>
</html>
