<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/user.php';

	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// アバター画像をimages/avatarに保存する
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// ファイル情報を変数に取得する
		$file = $_FILES['avatar'];
		$tmp_name = $file['tmp_name'];
		$error = $file['error'];

		// group_avatar名はランダムな文字列としてDBに保存する
		$name = uniqid(mt_rand(10, 19), true);

		// ファイル送信エラーがないか確認する
		if (!$error) {
			// group_avatarの登録処理
			if (isset($_POST['group_avatar'])) {
				// フォルダ内に同名ファイルがないか確認する
				if (file_exists((IMAGES_PATH.'/avatars/group_avatars/'.$name))) {
					exit('同名の画像ファイルがすでに存在します。');
				} else {
					$result = @move_uploaded_file($tmp_name, IMAGES_PATH.'/avatars/group_avatars/'.$name);
					if (!$result) {
						exit('ファイル送信エラーです。');
					} else {
						// group_avatarをgroupsに登録する
						update_group_avatar($name, $_POST['group_id'], $pdo);

						// セッションを再度設定する
						set_session('group_id', $_POST['group_id']);

						$pdo = null;
						header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/group_account.php');
					}
				}
			} else if (isset($_POST['user_avatar'])) {
				// フォルダ内に同名ファイルがないか確認する
				if (file_exists((IMAGES_PATH.'/avatars/user_avatars/'.$name))) {
					exit('同名の画像ファイルがすでに存在します。');
				} else {
					$result = @move_uploaded_file($tmp_name, IMAGES_PATH.'/avatars/user_avatars/'.$name);
					if (!$result) {
						exit('ファイル送信エラーです。');
					} else {
						// user_avatarをusersに登録する
						update_user_avatar($name, $_SESSION['user_id'], $pdo);

						$pdo = null;
						header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/account.php');
					}
				}
			}
			
		} else {
			exit('ファイル送信エラーです。');			
		}
	}
?>