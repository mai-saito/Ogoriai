<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';

	// セッション開始
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

		if (count($errors) !== 0) {
			// 入力エラーがあった場合、エラーを表示する
			notify_errors($errors);
		} else {
			// 支出情報をexpensesテーブルに挿入する
			var_dump($_POST['group_id']);
			insert_new_expense($pdo, $item, $amount, $_POST['group_id']);

			// 先ほど登録した最新の支出データをexpenseテーブルから取得する
			$last_expense = get_last_expense($pdo);

			// 取得した最新のexpenseデータをグループの人数で等分する
			$devided = devide_evenly($pdo, $last_expense, $_SESSION['user_id']);

			// 各ユーザーの繰越金額をアップデートする
			calculate_carryover($pdo, $devided, $last_expense);
		}
		$stmt = null;
	} else {
		$pdo = null;
		exit('直接アクセスは禁止です。');
	}
	$pdo = null;
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/mypage.php');
?>