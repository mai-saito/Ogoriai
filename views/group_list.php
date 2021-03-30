<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';

	// セッション開始
	// session_start();
	// check_session('email');

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// グループと繰越金データの配列を取得する
	$data = initialize_display($pdo, $_SESSION['user_id']);

	// ユーザーが所属しているグループのデータを取得　
	$groups = $data[0];

	// ユーザーの各グループに対する最新の繰越金額を取得
	$updated_carryover = $data[1];

	// グループごとに支出データの入力フォームを作成する
	if ($groups):
		foreach ($groups as $group):
			foreach ($updated_carryover as $carryover):
				if ($group['group_id'] === $carryover['group_id']):
?>
	<div class="group-list p-3">
	<form action="expense_table.php" method="POST">
		<p><span onclick="handleClick(event)"><?php echo $group['group_name'] ?></span>：<?php echo $carryover['carryover'] ?>円</p>
		<input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>">
	</form>	
	<form action="../add_expense.php" method="post">
		<table>
			<tr class="form-group">
				<th><label for="item">アイテム：</label></th>
				<td><input type="text" name="item" id="item" class="form-control" size="30"></td>
			</tr>
			<tr class="form-group">
				<th><label for="amount">金額：</label></th>
				<td><input type="text" name="amount" id="amount" class="form-control"></td>
				<td><p>円</p></td>
			</tr>
			<tr>
				<th><input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>"></th>
				<td class="text-right"><input type="submit" value="入力" class="btn btn-lg btn-primary mt-2"></td>
			</tr>
		</table>
	</form>
</div>
<?php
				endif;
			endforeach;
		endforeach;
?>
<?php else: ?>
<p>まだグループがありません</p>
<?php 
	endif;
	$stmt = null;
	$pdo = null;
?>
