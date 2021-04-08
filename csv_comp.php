<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';

	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (!isset($_POST['radio'])) {
			exit('どの種類のCSVを出力するか選択してください。');
		}

		// DBからCSV用の情報を取得する
		switch ($_POST['radio']) {
			case '0':
				$result = get_expense_list($pdo, $_POST['group_id']);
				break;

			case '1':
				$sql = 'SELECT e.item, e.amount, u.name, u.user_id, e.date, g.group_name, g.group_id, e.expense_id FROM expenses e LEFT OUTER JOIN users u ON u.user_id = e.user_id INNER JOIN groups g ON g.group_id = e.group_id WHERE e.group_id = :group_id AND e.user_id = :user_id';
				$stmt = $pdo -> prepare($sql);
				$stmt -> bindParam('group_id', $_POST['group_id']);
				$stmt -> bindParam('user_id', $_SESSION['user_id']);
				$stmt -> execute();
				$result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
				break;

			case '2':
				$sql = 'SELECT e.item, e.amount, u.name, e.date FROM expenses e LEFT OUTER JOIN users u ON u.user_id = e.user_id INNER JOIN groups g ON g.group_id = e.group_id WHERE e.user_id = :user_id';
				$stmt = $pdo -> prepare($sql);
				$stmt -> bindParam('user_id', $_SESSION['user_id']);
				$stmt -> execute();
				$result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
				break;
		
			default:
				var_dump($_POST['radio']);
				break;
		}

		// CSVの文字列を作成する
		$csv_string = "アイテム,金額,支払者,日付\r\n";

		if ($result) {
			foreach ($result as $value) {
				$csv_string .= $value['item'] . ","; 
				$csv_string .= $value['amount'] . ",";
				$csv_string .= $value['name'] . ",";
				$csv_string .= $value['date'] . "\r\n";
			}
		} else {
			$csv_string .= 'まだ支出データがありません。';
		}

		// 出力ファイルの名前と形式を指定する
		$file_name = 'file.csv';
		header('Content-Type: text/csv'); 
		header('Content-Disposition: attachment; filename='.$file_name);

		// CSVを出力する
		echo mb_convert_encoding($csv_string, 'SJIS', 'UTF-8');
	}
	$pdo = null;
	exit();
?>