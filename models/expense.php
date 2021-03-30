<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';
	
	//セッション確認
	// session_start();
	// check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// 新しい支出をexpensesテーブルに登録する
	function insert_new_expense($pdo, $item, $amount) {
		$stmt = null;
		$sql = 'INSERT INTO expenses (`user_id`, `group_id`, `item`, `amount`, `date`) VALUES (:user_id, :group_id, :item, :amount, :date)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':user_id', $_SESSION['user_id']);
		$stmt -> bindValue(':group_id', $_POST['group_id']);
		$stmt -> bindValue(':item', $item);
		$stmt -> bindValue(':amount', $amount);
		$stmt -> bindValue(':date', date("Y-m-d"));
		$stmt -> execute();
		$stmt = null;
	}

	// 1番新しく登録されたexpensesテーブルの行を取り出す
	function get_last_expense($pdo) {
		$stmt = null;
		$sql = 'SELECT * FROM expenses ORDER BY expense_id DESC LIMIT 1';
		$stmt = $pdo -> prepare($sql);
		$stmt -> execute();
		return $stmt -> fetch(PDO::FETCH_ASSOC);
	}

	// user_groupテーブルから各メンバーの現在の繰越額を取得する
	function get_carryover($pdo, $group_id) {
		$stmt = null;
		$sql = 'SELECT * FROM user_group WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// // user_groupテーブルから指定のグループ内の特定のメンバーの繰越額を取得する 
	// function get_specific_carryover($pdo, $group_id, $user_id) {
	// 	$stmt = null;
	// 	$sql = 'SELECT * FROM user_group WHERE group_id = :group_id AND user_id = :user_id';
	// 	$stmt = $pdo -> prepare($sql);
	// 	$stmt -> bindParam(':group_id', $group_id);
	// 	$stmt -> bindParam(':user_id', $user_id);
	// 	$stmt -> execute();
	// 	return $stmt -> fetch(PDO::FETCH_ASSOC);
	// }

	// user_groupテーブルから指定のメンバーの各グループの最新の繰越額を取得する
	function get_updated_carryover($pdo, $user_id) {
		$stmt = null;
		$sql = 'SELECT * FROM user_group WHERE user_id = :user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// グループごとの支出一覧を表示する
	function get_expense_list($pdo, $group_id) {
		$stmt = null;
		$sql = 'SELECT e.item, e.amount, u.name, u.user_id, e.date, g.group_name, g.group_id, e.expense_id FROM expenses e LEFT OUTER JOIN users u ON u.user_id = e.user_id INNER JOIN groups g ON g.group_id = e.group_id WHERE e.group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam('group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// グループ全体の合計金額を出す
	function get_group_total($pdo, $group_id) {
		$stmt = null;
		$sql = 'SELECT SUM(amount) AS group_total FROM expenses WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetch();
	}

	// 指定のユーザーの支払額を算出する
	function get_user_total($pdo, $group_id, $user_id) {
		$stmt = null;
		$sql = 'SELECT SUM(amount) AS subtotal, user_id FROM expenses WHERE group_id = :group_id AND user_id = :user_id GROUP BY user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		return $stmt -> fetch(PDO::FETCH_ASSOC);
	}

	// ユーザーごとの支払額を算出する
	function get_user_totals($pdo, $group_id) {
		$stmt = null;
		$sql = 'SELECT SUM(amount) AS member_subtotal, user_id FROM expenses WHERE group_id = :group_id GROUP BY user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// user_groupテーブルを最新の繰越額にアップデートする
	function update_carryover($pdo, $updated_carryover, $group_id, $user_id) {
		$stmt = null;
		$sql = 'UPDATE user_group SET carryover = :carryover WHERE group_id = :group_id AND user_id = :user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':carryover', $updated_carryover);
		$stmt -> bindParam(':group_id', $group_id);				
		$stmt -> bindParam(':user_id', $user_id);	
		$stmt -> execute();
		$stmt = null;
	}

	// メンバー全員の繰越額をDefault値にリセットする
	function reset_carryover_to_default($pdo, $group_id, $user_id) {
		$stmt = null;
		$sql = 'UPDATE user_group SET carryover = DEFAULT WHERE group_id = :group_id AND user_id = :user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);				
		$stmt -> bindParam(':user_id', $user_id);	
		$stmt -> execute();
		$stmt = null;
	}

	// expenseテーブルの支出情報を変更する
	function update_expense($pdo, $expense_id, $item, $amount, $user_id) {
		$stmt = null;
		$sql = 'UPDATE expenses SET item = :item, amount = :amount, user_id = :user_id, date = :date WHERE expense_id = :expense_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':item', $item);
		$stmt -> bindValue(':amount', $amount);
		$stmt -> bindValue(':user_id', $user_id);
		$stmt -> bindValue(':date', date("Y-m-d"));
		$stmt -> bindParam(':expense_id', $expense_id);
		$stmt -> execute();
		$stmt = null;
	}

	// expenseテーブルの支出情報を削除する
	function delete_expense($pdo, $expense_id) {
		$stmt = null;
		$sql = 'DELETE FROM expenses WHERE expense_id = :expense_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':expense_id', $expense_id);
		$stmt -> execute();
		$stmt = null;
	}

	// expensesテーブルから指定のgroup_idの支出行を削除する
	function delete_expense_by_group_id($pdo, $group_id) {
		$stmt = null;
		$sql = 'DELETE FROM expenses WHERE group_id = :group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		$stmt = null;
	}

	// マイページに表示するために必要な支出データを返す
	function initialize_display($pdo, $user_id) {
		$groups = get_group_member($pdo, $user_id);
		$updated_carryover = get_updated_carryover($pdo, $user_id);
		return ([$groups, $updated_carryover]);
	}

	// 支出額をグループのメンバー数で等分する
	function devide_evenly($pdo, $last_expense, $user_id) {
		$groups = get_group_member($pdo, $user_id);
		foreach ($groups as $group) {
			$members = array($group['group_id'] => $group['number_of_members']);
			foreach ($members as $group_id => $member) {
				if ($group_id === $last_expense['group_id']) {
					return $last_expense['amount'] / $member;
				}
			}
		}
	}

	// user_groupテーブルの繰越額を最新の金額にアップデートする
	function calculate_carryover($pdo, $devided, $last_expense) {
		// 現在の繰越額を取得する
		$result = get_carryover($pdo, $last_expense['group_id']);

		// 現在の繰越額を元に新しい支出額を加えて繰越額を再計算する
		foreach ($result as $value) {
			$stmt = null;
			
			// 今回分の支払いを行なったユーザーとそれ以外のユーザーで算出方法を分岐する
			if ($last_expense['user_id'] == $value['user_id']) {
				// 支払いを行なったユーザーの繰越額＝現在の繰越額＋（今回の支出等分額ー支払った額）
				$updated_carryover = $value['carryover'] + ($devided - $last_expense['amount']);			
			 } else {
				// 支払いを行なっていないユーザーの繰越額＝現在の繰越額＋今回の支出等分額
				$updated_carryover = $value['carryover'] + $devided;
			}

			// user_groupテーブルを最新の繰越額にアップデートする
			update_carryover($pdo, $updated_carryover, $last_expense['group_id'], $value['user_id']);
		}
	}

	// 各ユーザーの繰越金を再計算する
	function recalculate_carryover($pdo, $group_id, $user_id) {
		// グループ全体の合計金額を取得する
		$total = get_group_total($pdo, $group_id);

		if ($total['group_total']) {
			// グループのメンバー数を取得する
			$groups = get_group_member($pdo, $user_id);

			// グループの合計金額を等分する
			foreach ($groups as $group) {
				if ($group['group_id'] == $group_id){
					$number = $group['number_of_members'];
				}
			}
			$devided = $total['group_total'] / $number;

			// ユーザーごとの支払額を算出する
			$subtotal = get_user_totals($pdo, $group_id);

			// 等分額からユーザーごとの支払額を減ずる
			foreach ($subtotal as $value) {
				// 更新する繰越額を計算する
				$updated_carryover = $devided - $value['member_subtotal'];
			
				// 最新の繰越額にアップデートする
				update_carryover($pdo, $updated_carryover, $group_id, $value['user_id']);
			}
		} else {
			// メンバー全員のuser_idを取得する
			$members = get_member_names($pdo, $group_id);

			// メンバー全員の繰越額をDefault値にリセットする
			foreach ($members as $member) {
				reset_carryover_to_default($pdo, $group_id, $member['user_id']);
			}
		}	
	}

	// ユーザー脱退後の残りのメンバーの繰越金を再計算する
	function recalculate_carryover_after_resignation($pdo, $group_id, $remained_carryover) {
		// 脱退したユーザーが所属していたグループの人数を取得する
		$stmt = null;
		$sql = 'SELECT COUNT(u.user_id) AS number_of_members, g.group_name, g.group_id FROM users AS u INNER JOIN user_group AS u_g ON u_g.user_id = u.user_id INNER JOIN groups AS g ON g.group_id = u_g.group_id WHERE g.group_id = :group_id GROUP BY g.group_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':group_id', $group_id);
		$stmt -> execute();
		$result = $stmt -> fetch(PDO::FETCH_ASSOC);
		if ($result) {
			// 脱退したユーザーの繰越額を等分する
			if ($remained_carryover !== 0) {
				$devided = $remained_carryover / $result['number_of_members'];
			} else {
				$devided = 0;
			}
			
			// 現在の残りのメンバーの繰越額を取得する
			$members = get_carryover($pdo, $group_id);
			foreach ($members as $member) {
				// 現在の繰越額に脱退したユーザーの繰越額を上乗せする
				$updated_carryover = $member['carryover'] + $devided;

				// 残ったメンバーの繰越額を更新する
				update_carryover($pdo, $updated_carryover, $group_id, $member['user_id']);
			}
		}	
	}

?>