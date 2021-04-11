<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// ユーザーが所属する各グループの情報を取得する
	function get_groups($pdo, $user_id) {
		$stmt = null;
		$sql = 'SELECT COUNT(u.user_id) AS number_of_members, g.group_name, g.group_id, g.group_avatar, g.rounding FROM users AS u INNER JOIN user_group AS u_g ON u_g.user_id = u.user_id INNER JOIN groups AS g ON g.group_id = u_g.group_id WHERE g.group_id IN (SELECT g.group_id FROM users AS u INNER JOIN user_group AS u_g ON u_g.user_id = u.user_id INNER JOIN groups AS g ON g.group_id = u_g.group_id WHERE u_g.user_id = :user_id) GROUP BY g.group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// 指定のグループの情報を取得する
	function get_specific_group($pdo, $group_id) {
		$stmt = null;
		$sql = 'SELECT COUNT(u.user_id) AS number_of_members, g.group_name, g.group_id, g.rounding FROM users AS u INNER JOIN user_group AS u_g ON u_g.user_id = u.user_id INNER JOIN groups AS g ON g.group_id = u_g.group_id WHERE g.group_id = :group_id GROUP BY g.group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetch(PDO::FETCH_ASSOC);
	}

	// グループのメンバーの名前とuser_idを取得する
	function get_member_names($pdo, $group_id) {
		$stmt = null;
		$sql ='SELECT u.name, u.user_id, u.email FROM users AS u WHERE u.user_id IN (SELECT u_g.user_id FROM user_group AS u_g WHERE group_id = :group_id)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// グループ名からあいまい検索をかけて、グループのメンバーの名前とuser_idを取得する
	function get_member_names_with_keyword($pdo, $group_name) {
		$stmt = null;
		// usersテーブルにおいて名前かメールアドレスの一部であいまい検索を実行する
		$group_name = '%'.$group_name.'%';
		$sql = 'SELECT u.user_id, u.name, u.email u_g.group_leader, g.group_id, g.group_name FROM users AS u INNER JOIN user_group AS u_g ON u_g.user_id = u.user_id INNER JOIN groups AS g ON g.group_id = u_g.group_id WHERE group_name LIKE :group_name';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_name', $group_name);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// グループの情報を取得する
	function get_group_info($pdo, $group_id) {
		$stmt = null;
		$sql ='SELECT * FROM groups WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetch(PDO::FETCH_ASSOC);
	}

	// グループのリーダーを取得する
	function get_group_leader($pdo, $group_id) {
		$stmt = null;
		$sql ='SELECT user_id FROM user_group WHERE group_leader = 1 AND group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetch(PDO::FETCH_ASSOC);
	}

	// 新しくグループを作成する
	function create_group($pdo, $group_name, $rounding) {
		$stmt = null;
		$sql = 'INSERT INTO groups (group_name, rounding) VALUES (:group_name, :rounding)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':group_name', $group_name);
		$stmt -> bindValue(':rounding', $rounding);
		$stmt -> execute();
		$stmt = null;
	}

	// user_groupテーブルに選択されたユーザーを挿入する
	function add_member($pdo, $user_id, $group_id, $group_leader) {
		$stmt = null;
		$sql = 'INSERT INTO user_group (user_id, group_id, group_leader) VALUES (:user_id, :group_id, :group_leader)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':user_id', $user_id);
		$stmt -> bindValue(':group_id', $group_id);
		$stmt -> bindValue(':group_leader', $group_leader);
		$stmt -> execute();
		$stmt = null;
	}

	// group_nameをアップデートする
	function update_group_name($group_name, $group_id, $pdo) {
		$sql = 'UPDATE groups SET group_name = :group_name WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':group_name', $group_name);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		$stmt = null;
	}

	// group_avatarをアップデートする
	function update_group_avatar($group_avatar, $group_id, $pdo){
		$stmt = null;
		$sql = 'UPDATE groups SET group_avatar = :group_avatar WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':group_avatar', $group_avatar);
		$stmt -> bindParam(':group_id', $group_id);
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
	function delete_user_group_by_group($pdo, $group_id) {
		$stmt = null;
		$sql = 'DELETE FROM user_group WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		$stmt = null;
	}

	// user_groupテーブルから指定のuser_idとgroup_idの行を削除する
	function delete_specified_user_group($pdo, $group_id, $user_id) {
		$stmt = null;
		$sql = 'DELETE FROM user_group WHERE group_id = :group_id AND user_id = :user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		$stmt = null;
	}
	?>