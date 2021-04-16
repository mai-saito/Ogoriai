<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';	
	require_once MODELS_PATH.'/user.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID , LOCAL_PASSWORD);

	// 入力エラー配列
	$errors = array();

	if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		// メールアドレスの入力確認
		$email = null;
		if (!isset($_POST['email']) || !strlen($_POST['email'])){
			$errors['email'] = 'メールアドレスを入力してください';
		} else {
			$email = $_POST['email'];
		}

		// パスワードの入力確認
		$password = null;
		if (!isset($_POST['password']) || !strlen($_POST['password'])){
			$errors['password'] = 'パスワードを入力してください';
		} else {
			$password = $_POST['password'];
		}

		// メールアドレスがを確認する
		$result = check_email ($pdo, $email);
		if ($result) {
			// メールアドレスが一致したユーザーが管理者権限を持っているか確認する
			if ($result['admin'] == 1) {
				// パスワードを確認する
				if (password_verify($_POST['password'], $result['password'])){
					// セッション開始
					session_start();

					// セッションにユーザー情報を入力する
					set_session('user_id', $result['user_id']);
					set_session('name', $result['name']);
					set_session('email', $result['email']);
				} else {
					$errors['password'] = 'メールアドレスかパスワードが異なります。';
				}
			} else {
				$errors['admin'] = '管理画面にログインするには管理者権限のあるメールアドレスとパスワードが必要です。';
			}
		} else {
			$errors['email'] = 'メールアドレスかパスワードが異なります。';
		}
		$stmt = null;

		// 入力エラーがあった場合、エラーを表示する
		if (count($errors) !== 0) {
			notify_errors($errors);
		} else {
			$pdo = null;
			header("Location: http://".$_SERVER['HTTP_HOST']."/ogoriai/admin/views/dashboard.php");	
		}
	} else {
		$pdo = null;
		exit('直接アクセス禁止です。');
	}
	$pdo = null;
?>