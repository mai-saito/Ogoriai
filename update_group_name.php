<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';

	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// エラー入力配列
	$errors = array();

	// ユーザーの名前をアップデートする
	if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		// group_nameの入力内容を確認する
		$group_name = null;
		if (!isset($_POST['group_name']) || !strlen($_POST['group_name'])) {
			$errors['group_name'] = 'グループ名を入力してください。';
		} else if (strlen($_POST['group_name']) > 20) {
				$errors['group_name'] = 'グループ名は20文字以下で入力してください。';
		}	else {
				$group_name = $_POST['group_name'];
		}

		if (count($errors) != 0) {
			// エラーを出力する
			notify_errors($errors);
		} else {
			// group_nameをアップデートする
			update_group_name($group_name, $_POST['group_id'], $pdo);
		}
		$pdo = null;
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/group_account.php');
	}	else {
		$pdo = null;
		exit('直接アクセス禁止です。');
	}

?>