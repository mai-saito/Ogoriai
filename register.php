<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>登録</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID , LOCAL_PASSWORD);

	// 入力エラー配列
	$errors = array();

	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		// 同じメールアドレスがすでに登録されていないか確認する
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

		// 名前の入力内容を確認する
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

		// パスワードの入力内容を確認する
		$password = null;
		if (!isset($_POST['password']) || !strlen($_POST['password'])):
			$errors['password'] = 'パスワードを入力してください。';
		else: 
			if (!(preg_match('/^[a-zA-Z0-9]{6,12}$/', $_POST['password']) && preg_match('/[a-z]+/', $_POST['password']) && preg_match('/[A-Z]+/', $_POST['password']) && preg_match('/[0-9]+/', $_POST['password']))):
			$errors['password'] = 'パスワードは大文字・小文字を含む半角英数字で、6文字以上12文字以内で入力してください。';
			else:
			$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
			endif;
		endif;

		if (count($errors) !== 0):
			// 入力エラーがあった場合、エラーを表示する
			notify_errors($errors);
		else:
			// usersテーブルにuserを登録する
			$sql = 'INSERT INTO `users` (`name`, `password`, `email`) VALUES (:name, :password, :email)';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':name', $name);
			$stmt -> bindParam(':password', $password);
			$stmt -> bindParam(':email', $email);
			$stmt -> execute();
			$stmt = null;
			
			// セッション開始
			session_start();
			set_session('user_id', $pdo -> lastInsertId());
			set_session('name', $name);
			set_session('email', $email);
?>
	<h1><?php echo htmlspecialchars($name, ENT_QUOTES, 'utf-8') ?>さん、登録完了です。</h1>
	<p><a href="views/mypage.php">マイページへ</a></p>
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