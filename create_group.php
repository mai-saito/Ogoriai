<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';
	
	// セッション開始
	session_start();
	check_session('user_id');	
	var_dump($_SESSION);


	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// 入力エラー配列
	$errors = array();

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// group_nameの入力内容を確認する
		$group_name = null;
		if (!isset($_POST['group_name']) || !strlen($_POST['group_name'])) {
			$errors['group_name'] = 'グループ名を入力してください。';
		} else if (strlen($_POST['group_name']) > 20) {
				$errors['group_name'] = 'グループ名は20文字以下で入力してください。';
		}	else {
				$group_name = $_POST['group_name'];
		}

		// 端数処理方法を確認する
		$rounding = null; 
		if (!isset($_POST['rounding']) || !strlen($_POST['rounding'])) {
			$errors['rounding'] = '端数処理方法を選択してください。';
		} else {
			$rounding = $_POST['rounding'];
		}

		if (count($errors) !== 0) {
			// 入力エラーがあった場合、エラーを表示する
			notify_errors($errors);
		} else {
			// groupsテーブルに新しいグループを挿入する
			create_group($pdo, $group_name, $rounding);

			// セッション設定
			set_session('group_id', $pdo -> lastInsertId());
			set_session('group_name', $group_name);

			// クッキー設定
			setcookie('tab', 'tab1', time() + 300, '/ogoriai/views');
		}	
		// uset_groupテーブルにユーザーを追加する
		add_member($pdo, $_SESSION['user_id'], $_SESSION['group_id'], 1);

		
	} else {
		$pdo = null;
		exit('直接アクセスは禁止です。');
	}
	$pdo = null;
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/mypage.php');
?>