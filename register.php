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
	require_once MODELS_PATH.'/user.php';

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
					// 同じメールアドレスがないか確認する
					$result = check_email ($pdo, $_POST['email']);
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
	<div id="wrapper">
		<header>
			<h1><a href="../index.html"><img src="images/logo-sm.png" alt="ロゴ画像"></a></h1>
			<nav>
				<ul>
					<li><a href="views/mypage.php" class="mr-3">マイページ</a></li>
					<li><a href="views/account.php" class="mr-3">アカウント</a></li>
					<li><a href="logout.php">ログアウト</a></li>
				</ul>
			</nav>
		</header>
		<main class="register-comp">
			<h1><?php echo htmlspecialchars($name, ENT_QUOTES, 'utf-8') ?>さん、登録完了です。</h1>
			<p><a href="views/mypage.php" class="btn btn-lg btn-primary">マイページへ</a></p>
		</main>
		<footer>
			<div>
				<small>© 2021 Mai Saito.</small>
			</div>
			<div>
				<p><a href="views/faq.html" class="btn btn-lg btn-contact mr-3">よくあるご質問</a></p>
				<p><a href="views/contact.php"  class="btn btn-lg btn-contact mr-3">お問合せ</a></p>
				<p><a href="#wrapper"><img src="images/page-top-nude.png" alt="ページトップへ移動"></a></p>
			</div>
		</footer>
	</div>	
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