<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// 各グループのメンバー数を計算する
	function get_group_member($pdo, $user_id) {
		$stmt = null;
		$sql = 'SELECT COUNT(u.user_id) AS number_of_members, g.group_name, g.group_id FROM users AS u INNER JOIN user_group AS u_g ON u_g.user_id = u.user_id INNER JOIN groups AS g ON g.group_id = u_g.group_id WHERE g.group_id IN (SELECT g.group_id FROM users AS u INNER JOIN user_group AS u_g ON u_g.user_id = u.user_id INNER JOIN groups AS g ON g.group_id = u_g.group_id WHERE u_g.user_id = :user_id) GROUP BY g.group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// グループのメンバーの名前とuser_idを取得する
	function get_member_names($pdo, $group_id) {
		$stmt = null;
		$sql ='SELECT u.name, u.user_id FROM users AS u WHERE u.user_id IN (SELECT u_g.user_id FROM user_group AS u_g WHERE group_id = :group_id)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// グループのgroup_nameを取得する
	function get_group_name($pdo, $group_id) {
		$stmt = null;
		$sql ='SELECT group_name FROM groups WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetch(PDO::FETCH_ASSOC);
	}

	// user_groupテーブルに選択されたユーザーを挿入する
	function add_member($pdo, $user_id, $group_id) {
		$stmt = null;
		$sql = 'INSERT INTO `user_group` (`user_id`, `group_id`) VALUES (:user_id, :group_id)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':user_id', $user_id);
		$stmt -> bindValue(':group_id', $group_id);
		$stmt -> execute();
		$stmt = null;
	}

	// 指定のグループをgroupsテーブルから消去する
	function delete_group($pdo, $group_id) {
		$stmt = null;
		$sql = 'DELETE FROM groups WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		$stmt = null;
	}

	// user_groupテーブルから指定のgroup_idの行を削除する
	function delete_user_group($pdo, $group_id) {
		$stmt = null;
		$sql = 'DELETE FROM user_group WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		$stmt = null;
	}
	?>