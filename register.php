<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>登録</title>
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php 
// DB接続情報
require_once 'dsn.php';

$errors = array();

// DB登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST'):
	$email = null;
	if (!isset($_POST['email']) || !strlen($_POST['email'])):
		$errors['email'] = 'メールアドレスを入力してください。';
	else:
		if (strlen($_POST['email']) > 50):
			$errors['email'] = 'メールアドレスは50文字以下で入力してください。';
		else: 
			if (!preg_match('/^[a-zA-Z0-9]+[a-zA-Z0-9-_.]*@[a-zA-Z0-9-_]+.[a-zA-Z0-9-_.]+$/', $_POST['email'])):
				$errors['email'] = 'そのメールアドレスは無効です。';
			else:
				$sql = 'SELECT `email` FROM `users` WHERE `email` = :email';
				$stmt = $pdo -> prepare($sql);
				// print_r($pdo->errorInfo());
				$stmt -> bindValue(':email', $_POST['email']);
				$stmt -> execute();
				$result = $stmt -> fetch();
					if ($result):
						$errors['email'] = 'そのメールアドレスはすでに登録済みです。';
					else:
						$email = $_POST['email'];
					endif;
			 	$stmt = null;
			endif;
		endif;
	endif;

	$name = null;
	if (!isset($_POST['name']) || !strlen($_POST['name'])):
		$errors['name'] = '名前を入力してください。';
	else: 
		if (strlen($_POST['name']) > 20):
		$errors['name'] = '名前は20文字以内で入力してください。';
		else:
		$name = $_POST['name'];
		endif;
	endif;

	$password = null;
	if (!isset($_POST['password']) || !strlen($_POST['password'])):
		$errors['password'] = 'パスワードを入力してください。';
	else: 
		if (!(preg_match('/^[a-zA-Z0-9]{6,12}$/', $_POST['password']) && preg_match('/[a-z]+/', $_POST['password']) && preg_match('/[A-Z]+/', $_POST['password']) && preg_match('/[0-9]+/', $_POST['password']))):
		$errors['password'] = 'パスワードは大文字・小文字を含む半角英数字で、6文字以上12文字以内で入力してください。';
		else:
		$password = $_POST['password'];
		endif;
	endif;

	if (count($errors) === 0):
		$sql = 'INSERT INTO `users` (`name`, `password`, `email`) VALUES (:name, :password, :email)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':name', $name);
		$stmt -> bindParam(':email', $email);
		$stmt -> bindParam(':password', $password);
		$stmt -> execute();
		$stmt = null;
		
		// セッション開始
		session_start();
		session_regenerate_id(true);
		$_SESSION['name'] = $name;
		$_SESSION['email'] = $email;
?>
	<h1><?php echo htmlspecialchars($name, ENT_QUOTES, 'utf-8') ?>さん、登録完了です。</h1>
	<p><a href="mypage.php">マイページへ</a></p>
<?php	else: ?>
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
	exit('直接アクセスは禁止です。');
endif;
$pdo = null;
?>	
</body>
</html>