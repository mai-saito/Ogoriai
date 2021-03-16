<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ログイン</title>
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php 
// DB接続情報
require_once 'dsn.php';

$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST'):
	if (!isset($_POST['email']) || !strlen($_POST['email'])):
		$errors['email'] = 'メールアドレスを入力してください';
	endif;

	if (!isset($_POST['password']) || !strlen($_POST['password'])):
		$errors['password'] = 'パスワードを入力してください';
	endif;

	$sql = 'SELECT `name`, `email`, `password` FROM `users` WHERE `email` = :email';
	$stmt = $pdo -> prepare($sql);
	$stmt -> bindParam(':email', $_POST['email']);
	$stmt -> execute();
	$result = $stmt -> fetch();
	if ($result):
		if ($_POST['password'] === $result['password']):
			session_start();
			session_regenerate_id(true);
			$_SESSION['name'] = $result['name'];
			$_SESSION['email'] = $result['email'];
			$stmt = null;
			$pdo = null;
			header("Location: http://".$_SERVER['HTTP_HOST']."/ogoriai/mypage.php");
		else:
			$errors['password'] = 'メールアドレスかパスワードが異なります。';
		endif;
	else:
		$errors['email'] = 'メールアドレスかパスワードが異なります。';
	endif;
	$stmt = null;

	if (isset($errors)):
?>
	<ul>
<?php foreach ($errors as $error): ?>
		<li><?php echo $error ?></li>
<?php endforeach; ?>
	</ul>
	<input type="submit" value="戻る" onclick="history.go(-1)">
<?php
	endif;
else:
	$pdo = null;
	exit('直接アクセス禁止です。');
endif;
$pdo = null;
?>
</body>
</html>