<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';

	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// エラー入力配列
	$errors = array();

	// ユーザーの名前をアップデートする
	if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		// user_idがPOSTされているか確認する
		if (isset($_POST['user_id'])) {
			$user_id = $_POST['user_id'];
		} else {
			$errors['user_id'] = '送信エラーです。';
		}

		if (count($errors) != 0) {
			// エラーを出力する
			notify_errors($errors);
		} else {
			// 指定ユーザーのadminカラムをアップデートする
			$sql = 'UPDATE users SET admin = 1 WHERE user_id = :user_id';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':user_id', $user_id);
			$stmt -> execute();
			$stmt = null;
		}
		$pdo = null;
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/admin/dashboard.php');
	}	else {
		$pdo = null;
		exit('直接アクセス禁止です。');
	}

?>