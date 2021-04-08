<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/expense.php';

	// セッション確認
	session_start();
	check_session('group_id');

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// 入力エラー配列
	$errors = array();

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		// クッキーのgroup_idを変更する
		unset($_COOKIE['group_id']);
		// ユーザーの所属しているグループを取得する
		$groups = get_groups($pdo, $_SESSION['user_id']);
		$array = array();
		if ($groups) {
			foreach ($groups as $group) {
				var_dump($group['group_id']);
				if ($_POST['group_id'] != $group['group_id']) {
					array_push($array, $group['group_id']);
				}
			}
		$group = reset($array);
		var_dump($array);
		setcookie('group_id', $group, time() + 1000, '/');
		var_dump($group);
		var_dump($_COOKIE);
	}

		// 確認事項にチェックが入っているか確認する
		if (!isset($_POST['checkbox'])) {
			$errors['checkbox'] = '確認事項にチェックを入れてください。';
		} 

		// 入力エラーがあった場合、エラーを表示する
		if (count($errors) !== 0) {
			notify_errors($errors);
		} else {
			// 指定のグループをgroupsテーブルから消去する
			delete_group($pdo, $_POST['group_id']);

			// user_groupテーブルから指定のgroup_idの行を削除する
			delete_user_group($pdo, $_POST['group_id']);
			
			// expensesテーブルから指定のgroup_idの支出行を削除する
			delete_expense_by_group_id($pdo, $_POST['group_id']);
		}
	} else {
		$pdo = null;
		exit('直接アクセスは禁止です。');
	}
	$pdo = null;
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/mypage.php');
?>