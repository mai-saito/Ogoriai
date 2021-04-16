<?php 

// データベースに接続する
function connect_db($dsn, $id, $pw) {
	try {
		$pdo = new PDO($dsn, $id, $pw);
		$pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		exit('データベースに接続できません。'.$e -> getMessage());
	}
	return $pdo;
}

// セッションが切れている場合の処理を行う
function check_session($name) {
	session_regenerate_id(true);
	if (!isset($_SESSION[$name])) {
		echo 'セッションが切れています。<br>再度ログインをお願いいたします。';
		echo '<p><a href=http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/login.html>戻る</a></p>';
		exit();
	}
}

// セッションが切れている場合の処理を行う
function check_admin_session($name) {
	session_regenerate_id(true);
	if (!isset($_SESSION[$name])) {
		echo 'セッションが切れています。<br>再度ログインをお願いいたします。';
		echo '<p><a href=http://'.$_SERVER['HTTP_HOST'].'/ogoriai/admin/index.html>戻る</a></p>';
		exit();
	}
}

// セッションを任意の値にセットする
function set_session($name, $value) {
	$_SESSION[$name] = $value;
}

// 入力エラーと「戻る」ボタンを表示する
function notify_errors(array $errors) {
?>
	<ul>
<?php foreach ($errors as $error): ?>
			<li><?php echo $error ?></li>
<?php endforeach; ?>
		</ul>
		<input type="submit" value="戻る" onclick="history.go(-1)">
<?php
	exit();
}

// メールを送信する
function send_email($email, $subject, $message, $header) {
	// 文字コードと言語を指定する
	mb_language('Japanese');
	mb_internal_encoding('UTF-8');

	// メールを送信する
	// mb_send_mail($email, $subject, $message, $header);
	if (mb_send_mail($email, $subject, $message, $header)) {
		return true;
	} else {
		return false;
	}
}

// 絶対パスを設定する
function get_paths(array $paths) {
	$path = implode(DIRECTORY_SEPARATOR, $paths);
	return $path;
}
// 各ディレクトリまでの絶対パス
define('MODELS_PATH', dirname(__FILE__));
define('ROOT_PATH', get_paths([MODELS_PATH, '../']));
define('VIEWS_PATH', get_paths([MODELS_PATH, '..', 'views']));
define('CONFIG_PATH', get_paths([MODELS_PATH, '..', 'config']));
define('IMAGES_PATH', get_paths([MODELS_PATH, '..', 'images']));
define('ADMIN_PATH', get_paths([MODELS_PATH, '..', 'admin']));

?>
