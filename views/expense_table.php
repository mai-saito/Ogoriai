<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';
	
	// セッション確認
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// mypageで選択されたグループのgroup_idを取得する
	if (isset($_POST['group_id'])) {
		$group_id = $_POST['group_id']; 
	} else {
		$group_id = $_SESSION['updated_group_id'];
	}
	unset($_SESSION['updated_group_id']);

	// セッションにgroup_idを設定する
	$group = get_group_name($pdo, $group_id);
	set_session('group_id', $group_id);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>グループ支出一覧</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
	<header>
		<h1>おごりあい</h1>
		<nav>
			<ul>
				<li><a href="mypage.php">マイページ</a></li>
				<li><a href="account.php">アカウント</a></li>
				<li><a href="../logout.php">ログアウト</a></li>
			</ul>
		</nav>
	</header>
	<main class="expense-table">
<?php
	// グループメンバーの名前を取得する
	$members = get_member_names($pdo, $group_id);

	// グループ全体の合計金額を出す
	$group_total = get_group_total($pdo, $group_id);

	// ログインしているユーザーの支払額を算出する
	$subtotal = get_user_total($pdo, $group_id, $_SESSION['user_id']);
	if (!$subtotal) {
		$subtotal = 0;
	} else {
		$subtotal = $subtotal['subtotal'];
	}
	
	// 各メンバーの支払額を算出する
	$member_subtotals = get_user_totals($pdo, $group_id);

	// expenseテーブルのデータを取得する
	$result = get_expense_list($pdo, $group_id);

	// 一覧結果を使ってテーブルを作成する
	if ($result):
?>
		<h1>『<?php echo $group['group_name'] ?>』支出一覧</h1>
		<ul>
			<li><p id="modal-button">グループ管理</p></li>
			<li><a href="settle_up.php">精算する</a></li>
			<li><a href="mypage.php">マイページに戻る</a></li>
		</ul>
		<!-- 支出テーブル一覧 -->
		<table>
			<tr><th>アイテム</th><th>金額</th><th>メンバー</th><th>日付</th></tr>
<?php foreach ($result as $value): ?>
			<tr>
				<form action="../edit_expense.php" method="POST">
					<td><input type="text" name="item" value="<?php echo $value['item'] ?>"></td>
					<td><input type="text" name="amount" value="<?php echo $value['amount'] ?>"></td>
					<td>
						<select name="name">
<?php $name = (is_null($value['name'])) ? '退会済みユーザー' : $value['name']; ?>
							<option value="<?php echo $name ?>"><?php echo $name ?></option>
<?php 
	foreach ($members as $member):
		if ($value['user_id'] !== $member['user_id']):
?>
							<option value="<?php echo $member['name'] ?>"><?php echo $member['name'] ?></option>
<?php endif; ?>
<?php endforeach; ?>
						</select>
					</td>
					<td><input type="text" name="date" value="<?php echo $value['date'] ?>" readonly></td>
					<td><input type="hidden" name="group_id" value="<?php echo $value['group_id'] ?>"></td>
					<td><input type="hidden" name="expense_id" value="<?php echo $value['expense_id'] ?>"></td>
					<td><input type="submit" name="update" value="変更"></td>
					<td><input type="submit" name="delete" value="削除"></td>
				</form>
			</tr>
<?php endforeach; ?>
			</table>
			<!-- 合計金額テーブル -->
			<table>
				<tr>
					<th>グループ合計：</th>
					<td><?php echo $group_total['group_total'] ?>円</td>
				</tr>
				<tr>
					<th>あなたの支払い合計：</th>
					<td><?php echo $subtotal ?>円</td>
				</tr>
			</table>
<?php else: ?>
	<h1>『<?php echo $group['group_name'] ?>』支出一覧</h1>
	<ul>
		<li><p id="modal-button">グループの詳細</p></li>
		<li><a href="mypage.php">マイページに戻る</a></li>
	</ul>
	<p>支出データが存在しません。</p>
<?php endif; ?>
		<!-- グループ詳細モーダル -->
		<div class="modal" id="setting-modal">
			<div class="modal-content">
				<span class="close clearfix" id="close-setting">&times;</span>
				<div>
					<table>
						<tr>
							<th>グループ名：</th>
							<td><?php echo $group['group_name'] ?></td>
						</tr>
						<tr>
							<th>メンバー：</th>
							<td>
<?php 
	if ($members){
		foreach ($members as $member) {
			echo $member['name'],'<br>';
		}
	} else {
		echo 'メンバーがいません。';
	}
?>
							</td>
						</tr>
						<tr>
							<th>わりかん方法：</th>
							<td>等分</td>
						</tr>
					</table>
					<ul>
						<li><a href="choose_member.php">メンバーの追加</a></li>
						<li><a href="delete_group.php">グループの削除</a></li>
					</ul>
				</div>
			</div>
		</div>
	</main>
	<script src="../script.js"></script>
</body>
</html>