<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';

	// セッション確認
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// 入力エラー配列
	$errors = array(); 

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// アイテムの入力を確認する
		$item = null;
		if (!isset($_POST['item']) || !strlen($_POST['item'])) {
			$errors['item'] = 'アイテム欄を入力してください。';
		} else if (strlen($_POST['item']) > 30) {
			$errors['item'] = 'アイテムは30文字以内で入力してください。';
		} else {
			$item = $_POST['item'];
		}

		// 金額の入力を確認する
		$amount = null;
		if (!isset($_POST['amount']) || !strlen($_POST['amount'])) {
			$errors['amount'] = '金額を入力してください。';
		} else if (!preg_match('/^[0-9]+$/', $_POST['amount'])) {
			$errors['amount'] = '金額は半角数字で入力してください。';
		}else {
			$amount = $_POST['amount'];
		}

		// 名前の入力を確認する
		if (isset($_POST['name'])) {
			$members = get_member_names($pdo, $_POST['group_id']);
			foreach ($members as $member) {
				if ($member['name'] === $_POST['name']) {
					$user_id = $member['user_id'];
				}
			}
		}

		if (count($errors) !== 0) {
			// 入力エラーがあった場合、エラーを表示する
			notify_errors($errors);
		} else {
			// ボタンによって処理を分岐する
			if (isset($_POST['update'])) {
				// 指定の支出情報を変更する
				update_expense($pdo, $_POST['expense_id'], $item, $amount, $user_id);
			} else if (isset($_POST['delete'])) {
				// 指定の支出情報を削除する
				delete_expense($pdo, $_POST['expense_id']);
			} else {
				exit('この処理は無効です。');
			}						
			// expenseテーブルを再計算する
			recalculate_carryover($pdo, $_POST['group_id'], $_SESSION['user_id']);
		}

	} else {
		$pdo = null;
		exit('直接アクセスは禁止です。');
	}
	// expense_tableを再更新して表示するためのgroup_idをセッションに設定する
	set_session('updated_group_id', $_POST['group_id']);

	$pdo = null;
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/expense_table.php');
?>