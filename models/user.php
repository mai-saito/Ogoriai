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

	// ユーザーの名前かメールアドレスからあいまい検索をかける
	function get_user_info_with_keyword($pdo, $keyword) {
		$stmt = null;
		// usersテーブルにおいて名前かメールアドレスの一部であいまい検索を実行する
		$keyword = '%'.$keyword.'%';
		$sql = 'SELECT * FROM `users` WHERE `name` LIKE :name OR `email` LIKE :email';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':name', $keyword);
		$stmt -> bindParam(':email', $keyword);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
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

	// ユーザーをusersテーブルから削除する
	function delete_user($pdo, $user_id) {
		$stmt = null;
		$sql = 'DELETE FROM `users` WHERE `user_id` = :user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		$stmt = null;
	}
	// ユーザーをuser_groupテーブルから削除する
	function delete_user_group($pdo, $user_id) {
		$stmt = null;
		$sql = 'DELETE FROM `user_group` WHERE `user_id` = :user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		$stmt = null;
	}
?>
