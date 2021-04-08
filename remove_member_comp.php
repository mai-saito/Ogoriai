<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';

	// セッション確認
	session_start();
	check_session('group_id');

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// 指定のユーザーの繰越金額を取得する
		$carryovers = get_updated_carryover($pdo, $_POST['user_id']);
		var_dump($carryovers);
		foreach ($carryovers as $carryover) {
			if ($carryover['group_id'] == $_POST['group_id']) {
				$remained_carryover = $carryover['carryover'];
				var_dump($remained_carryover);
			}
		}

		// 指定のユーザーをグループから削除する
		delete_specified_user_group($pdo, $_POST['group_id'], $_POST['user_id']);
		var_dump($remained_carryover);

		// グループの繰越金を再計算する
		recalculate_carryover_after_user_dropped($pdo, $_POST['group_id'], $remained_carryover);

		// 自分がグループから抜ける場合
		if ($_SESSION['user_id'] == $_POST['user_id']) {
			$members = get_member_names($pdo, $_POST['group_id']);
			if (!$members) {
				// 指定のグループをgroupsテーブルから消去する
				delete_group($pdo, $_POST['group_id']);
			}
			$pdo = null;
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/mypage.php');
		}
	} else {
		$pdo = null;
		exit('直接アクセスは禁止です。');
	}
	$pdo = null;
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/remove_member.php');
?>