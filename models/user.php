<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// ユーザの情報を取得する
	function get_user_info($user_id, $pdo) {
		$stmt = null;
		$sql = 'SELECT * FROM users WHERE user_id = :user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		return $stmt -> fetch(PDO::FETCH_ASSOC);
	}

	// 同じメールアドレスがないか確認する
	function check_email ($pdo, $email) {
		$stmt = null;
		$sql = 'SELECT * FROM `users` WHERE `email` = :email';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':email', $email);
		$stmt -> execute();
		return $stmt -> fetch();
	}

	// user_avatarをアップロードする
	function update_user_avatar($user_avatar, $user_id, $pdo) {
		$stmt = null;
		$sql = 'UPDATE users SET user_avatar = :user_avatar WHERE user_id = :user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':user_avatar', $user_avatar);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		$stmt = null;
	}
?>
