<?php

/**
* 税込み価格へ変換する(端数は切り上げ)
* @param str  $price 税抜き価格
* @return str 税込み価格
*/
function price_before_tax($price) {

    return ceil($price * TAX);
}

/**
* 商品の値段を税込みに変換する(配列)
* @param array  $assoc_array 税抜き商品一覧配列データ
* @return array 税込み商品一覧配列データ
*/
function price_before_tax_assoc_array($assoc_array) {

    foreach ($assoc_array as $key => $value) {
        // 税込み価格へ変換(端数は切り上げ)
        $assoc_array[$key]['price'] = price_before_tax($assoc_array[$key]['price']);
    }
    return $assoc_array;
}

/**
* 特殊文字をHTMLエンティティに変換する
* @param str  $str 変換前文字
* @return str 変換後文字
*/
function entity_str($str) {

    return htmlspecialchars($str, ENT_QUOTES, HTML_CHARACTER_SET);
}

/**
* 特殊文字をHTMLエンティティに変換する(2次元配列の値)
* @param array  $assoc_array 変換前配列
* @return array 変換後配列
*/
function entity_assoc_array($assoc_array) {

    foreach ($assoc_array as $key => $value) {
        foreach ($value as $keys => $values) {
            // 特殊文字をHTMLエンティティに変換
            $assoc_array[$key][$keys] = entity_str($values);
        }
    }
    return $assoc_array;
}


/**
* DBハンドルを取得
* @return obj $link DBハンドル
*/
function get_db_connect() {

    try {
        // データベースに接続
        $dbh = new PDO(DNS, DB_USER, DB_PASSWD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        echo '接続できませんでした。理由：'.$e->getMessage();
    }
    return $dbh;
}


/**
* クエリを実行しその結果を配列で取得する
*
* @param obj  $link DBハンドル
* @param str  $sql SQL文
* @return array 結果配列データ
*/
function get_as_array($dbh, $sql) {

    try {
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQLを実行
        $stmt->execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo '接続できませんでした。理由：'.$e->getMessage();
    }
    return $rows;
}

/**
* 商品の一覧を取得する
*
* @param obj $link DBハンドル
* @return array 商品一覧配列データ
*/
function get_goods_table_list($link) {

    // SQL生成
    $sql = 'SELECT goods_name, price FROM goods_table';

    // クエリ実行
    return get_as_array($link, $sql);
}

/**
* insertを実行する
*
* @param obj $link DBハンドル
* @param str SQL文
* @return bool
*/
function insert_db($dbh, $sql) {

    try {
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQLを実行
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
    return true;
}


/**
* リクエストメソッドを取得
* @return str GET/POST/PUTなど
*/
function get_request_method() {

    return $_SERVER['REQUEST_METHOD'];
}

/**
* POSTデータを取得
* @param str $key 配列キー
* @return str POST値
*/
function get_post_data($key) {

    $str = '';
    if (isset($_POST[$key]) === TRUE) {
        $str = $_POST[$key];
    }
    return $str;
}


/**
* 名前が正しく入力されているかチェック
* @param str $user_name 名前
* @return mixed
*/
function check_user_name($user_name) {

    if (mb_strlen($user_name) === 0){
        return '名前を入力してください';
    } elseif (mb_strlen($user_name) > 20){
        return '名前は20文字以内で入力してください';
    } else {
        return true;
    }
}


/**
* ひとことが正しく入力されているかチェック
* @param str $user_comment ひとこと
* @return mixed
*/
function check_user_comment($user_comment) {

    if (mb_strlen($user_comment) === 0){
        return 'ひとことを入力してください';
    } elseif (mb_strlen($user_comment) > 100){
        return 'ひとことは100文字以内で入力してください';
    } else {
        return true;
    }
}


/**
* 掲示板へ書き込みを追加する
*
* @param obj $link DBハンドル
* @param str $user_name 名前
* @param int $user_comment ひとこと
* @param int $date 日付
* @return bool
*/
function insert_post($dbh, $user_name, $user_comment, $now_date) {

    try {
        // SQL生成
        $sql = 'INSERT INTO post(user_name, user_comment, create_datetime) VALUES(?, ?, ?)';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $user_name,    PDO::PARAM_STR);
        $stmt->bindValue(2, $user_comment, PDO::PARAM_STR);
        $stmt->bindValue(3, $now_date, PDO::PARAM_STR);
        // SQLを実行
        $stmt->execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        throw $e;
    }
}


/**
* 掲示板の書き込み一覧を取得する
*
* @param obj $link DBハンドル
* @return array 掲示板の書き込み一覧配列データ
*/
function get_post_list($link) {

    // SQL生成
    $sql = 'SELECT user_name, user_comment, create_datetime FROM post order by create_datetime desc ';
    
    // クエリ実行
    return get_as_array($link, $sql);
}


/**
* 前後の空白を削除
* @param str $str 文字列
* @return str 前後の空白を削除した文字列
*/
function trim_space($str) {

    return preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $str);
}
