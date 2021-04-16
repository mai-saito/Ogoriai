<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/notice.php';

	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// エラー入力配列
	$errors = array();

	if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		// 件名の入力確認をする
		if (!isset($_POST['title']) || !strlen($_POST['title'])) {
			$errors['title'] = '件名を入力してください。';
		} else if (strlen($_POST['title']) > 50) {
			$errors['title'] = '件名は50文字以内で入力してください。';
		} else {
			$title = $_POST['title'];
		}

		// コンテンツの入力確認をする
		if (!isset($_POST['content']) || !strlen($_POST['content'])) {
			$errors['content'] = '件名を入力してください。';
		} else {
			$content = $_POST['content'];
		}

		// 宛先の確認をする
		if (!isset($_POST['recipient']) || !strlen($_POST['recipient'])) {
			$errors['recipient'] = '宛先を選択してください。';
		} 

		if (count($errors) != 0) {
			// エラーを出力する
			notify_errors($errors);
		} else {
			// noticesテーブルに登録する
		  insert_notice($pdo, $title, $content);

			// notice_idを変数に保持する
			$notice_id = $pdo->lastInsertId();

			// user_noticeテーブルに送り先を登録する
			switch ($_POST['recipient']) {
				case '0':
					$sql = 'SELECT user_id FROM users';
					$stmt = $pdo -> query($sql);
					$users = $stmt -> fetchAll(PDO::FETCH_ASSOC);
					if ($users){
						// user_noticeテーブルに入力する
						foreach ($users as $user) {
							insert_user_notice($pdo, $notice_id, $_SESSION['user_id'], $user['user_id']);
						}
					}
					break;
				case '1':
					$group_id = $_POST['selected_recipient'];
					// グループのメンバーのuser_idを取得する
					$members = get_member_names($pdo, $group_id);
					if ($members) {
						foreach ($members as $member) {
							insert_user_notice($pdo, $notice_id, $_SESSION['user_id'], $member['user_id']);
						}
					}
					break;
				case '2':
					$user_id = $_POST['selected_recipient'];
					// user_noticeテーブルに入力する
					insert_user_notice($pdo, $notice_id, $_SESSION['user_id'], $user_id);
					break;
				
				default:
					var_dump($_POST['recipient']);
					break;
			}
		}
		$pdo = null;
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/admin/views/dashboard.php');
	}	else {
		$pdo = null;
		exit('直接アクセス禁止です。');
	}

?>