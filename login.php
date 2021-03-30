<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID , LOCAL_PASSWORD);

	// 入力エラー配列
	$errors = array();

	if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		if (!isset($_POST['email']) || !strlen($_POST['email'])){
			$errors['email'] = 'メールアドレスを入力してください';
		}

		if (!isset($_POST['password']) || !strlen($_POST['password'])){
			$errors['password'] = 'パスワードを入力してください';
		}

		// ログイン処理
		$sql = 'SELECT * FROM `users` WHERE `email` = :email';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':email', $_POST['email']);
		$stmt -> execute();
		$result = $stmt -> fetch();
		if ($result){
			if (password_verify($_POST['password'], $result['password'])){
				// セッション開始
				session_start();
				set_session('user_id', $result['user_id']);
				set_session('name', $result['name']);
				set_session('email', $result['email']);

				$stmt = null;
				$pdo = null;
				header("Location: http://".$_SERVER['HTTP_HOST']."/ogoriai/views/mypage.php");
			} else {
				$errors['password'] = 'メールアドレスかパスワードが異なります。';
			}
		} else {
			$errors['email'] = 'メールアドレスかパスワードが異なります。';
		}
		$stmt = null;
		
		// 入力エラーがあった場合、エラーを表示する
		if (count($errors) !== 0) {
			notify_errors($errors);
		}
	} else {
		$pdo = null;
		exit('直接アクセス禁止です。');
	}
	$pdo = null;
?>