<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';

	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// 入力エラー配列
	$errors = array();

	// ユーザーのパスワードを変更する
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// 現在のパスワードの入力確認
		$current_password = null;
		if (!isset($_POST['current_password']) || !strlen($_POST['current_password'])) {
			$errors['current_password'] = '現在のパスワードを入力してください。';
		}

		// 新しいパスワードの入力確認
		$new_password = null;
		if (!isset($_POST['new_password']) || !strlen($_POST['new_password'])) {
			$errors['new_password'] = '新しいパスワードを入力してください。';
		} else if (!(preg_match('/^[a-zA-Z0-9]{6,12}$/', $_POST['new_password']) && preg_match('/[a-z]+/', $_POST['new_password']) && preg_match('/[A-Z]+/', $_POST['new_password']) && preg_match('/[0-9]+/', $_POST['new_password']))) {
			$errors['new_password'] = 'パスワードは大文字・小文字を含む半角英数字で、6文字以上12文字以内で入力してください。';
		} else if ($_POST['new_password'] !== $_POST['new_password2']) {
			$errors['new_password2'] = '確認用の新しいパスワードが一致しません。';
		} else {
			$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);	
		}

		// 現在のパスワードを照合する
		if (count($errors) === 0) {
			$sql = 'SELECT password FROM users WHERE user_id = :user_id';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':user_id', $_SESSION['user_id']);
			$stmt -> execute();
			$result = $stmt -> fetch();
			if ($result) {
				// 現在のパスワードが合致した場合、新しいパスワードにアップデートする
				if (password_verify($_POST['current_password'], $result['password'])){
					$stmt = null;
					$sql = 'UPDATE users SET password = :password WHERE user_id = :user_id';
					$stmt = $pdo -> prepare($sql);
					$stmt -> bindValue(':password', $new_password);
					$stmt -> bindParam('user_id', $_SESSION['user_id']);
					$stmt -> execute();
				} else {
					exit('パスワードが合致しません。');
				}
				$stmt = null;
			} else {
				$errors['current_password'] = '現在のパスワードが異なっています。';
			}
		} else {
			// 入力エラーがあった場合、エラーを表示する
			notify_errors($errors);
		}
	} else {
		$pdo = null;
		exit('直接アクセス禁止です。');
	}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>パスワード変更</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
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
		<main class="comp">
			<p class="mb-3">パスワード変更完了です。</p>
			<p><a href="views/account.php" class="btn btn-lg btn-primary">アカウントページへ戻る</a></p>
		</main>
		<footer>
			<div>
				<small>© 2021 Mai Saito.</small>
			</div>
			<div>
				<p><a href="#"  class="btn btn-lg btn-contact mr-3">お問合せ</a></p>
				<p><a href="#wrapper"><img src="images/page-top-nude.png" alt="ページトップへ移動"></a></p>
			</div>
		</footer>
	</div>
</body>
</html>