<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ogoriai/models/function.php';
require_once CONFIG_PATH . '/env.php';
require_once MODELS_PATH . '/expense.php';

// セッション開始
// session_start();
// check_session('email');

// DB接続情報
$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

// グループと繰越金データの配列を取得する
$data = initialize_display($pdo, $_SESSION['user_id']);

// group_idを設定する
// header('Content-type: application/json; charset=utf-8');
// $data = filter_input(INPUT_POST, 'group_id');
// $group_id = json_encode($data);

if (!empty($_POST['group_id'])) {
	$group_id = $_POST['group_id'];
} else {
	$group_id = 1;
}

// var_dump($group_id);


// グループのメンバーの名前とuser_idを取得する
$members = get_member_names($pdo, $group_id);

// user_groupテーブルからログインしているユーザーの各グループの最新の繰越額を取得する
$carryover = get_updated_carryover($pdo, $_SESSION['user_id']);
// var_dump($carryover);

// グループの最新5件の支出情報を表示する
$expenses = get_first_5_expense_list($pdo, $group_id);
// var_dump($expenses);

// ユーザーの各グループに対する最新の繰越金額を取得
$updated_carryover = $data[1];

// ユーザーが所属しているグループのデータを取得　
$groups = $data[0];
if ($groups) :
?>
	<div class="group-list tab-panel">
		<!-- タブリスト -->
		<div class="tab-list mb-5">
<?php foreach ($groups as $group) : ?>
			<div class="tab">
				<input type="radio" name="tab" id="<?php echo $group['group_id'] ?>" value="<?php echo $group['group_id'] ?>">	
				<label for="<?php echo $group['group_id'] ?>" class="<?php echo $group['group_id'] ?>" onclick="handleClick(event)"><?php echo $group['group_name'] ?></label>
			</div>
<?php endforeach; ?>
		</div>
<?php endif; ?>
		<!-- 切り替えるタブの内容 -->
		<div class="panel-group">
			<div class="left-container">
				<div>
					<p><?php echo $_SESSION['name'] ?>さんの繰越金額</p>
<?php 
	foreach ($carryover as $value):
		if ($value['group_id'] === $group_id):
?>
					<p><?php echo $value['carryover'] ?>円</p>
<?php 
		endif;
	endforeach;
?>
				</div>
				<ul class="member-list['carryover']">
<?php
	// グループごとに支出データの入力フォームを作成する
	if ($members) :
		foreach ($members as $member) :
?>
					<li><?php echo $member['name'] ?></li>
<?php
		endforeach;
	endif;
?>
				</ul>
			</div>
			<div class="right-container">
				<!-- グループ管理メニュー -->
				<ul class="panel-menu">
					<li><p class="btn btn-lg btn-primary">一覧を表示</a></li>
					<li><a href="#" class="btn btn-lg btn-primary">清算する</a></li>
					<li>
						<form action="choose_member.php" method="POST" class="m-0">
							<input type="hidden" name="group_id" value="<?php echo $group_id ?>">
							<input type="submit" value="メンバー追加" class="btn btn-lg btn-primary">
						</form>
					<li><a href="delete_group.php" class="btn btn-lg btn-primary">グループを削除</a></li>
				</ul>
				<!-- 新しい支出情報の登録フォーム -->
				<form action="../add_expense.php" method="post">
					<input type="text" name="item" id="item" class="form-control mr-3" size="20" placeholder="アイテム">
					<input type="text" name="amount" id="amount" class="form-control mr-3" size="10" placeholder="金額">
					<input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>">
					<input type="submit" value="入力" class="btn btn-primary">
				</form>
				<!-- 最新5件の支出情報 -->
				<table class="mb-5">
					<tr><th>名前</th><th>日時</th><th class="amount">金額</th><th class="item">アイテム</th></tr>
<?php	foreach ($expenses as $expense): ?>
					<tr>
						<td><?php echo $expense['name'] ?></td>
						<td><?php echo $expense['date'] ?></td>
						<td class="amount"><?php echo $expense['amount'] ?></td>
						<td class="item"><?php echo $expense['item'] ?></td>
					</tr>
<?php	endforeach;?>
				</table>
			</div>
		</div>
	</div>
<?php
// 				endif;
// 			endforeach;
// 		endforeach;
// else :
?>
			<!-- <p>まだグループがありません</p> -->
<//?php	endif; ?>


<?php
	// グループごとに支出データの入力フォームを作成する
	if ($groups) :
		foreach ($groups as $group) :
			foreach ($updated_carryover as $carryover) :
				if ($group['group_id'] === $carryover['group_id']) :
	?>
					<!-- <div class="group-list p-3">
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
									<td>
										<p>円</p>
									</td>
								</tr>
								<tr>
									<th><input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>"></th>
									<td class="text-right"><input type="submit" value="入力" class="btn btn-lg btn-primary mt-2"></td>
								</tr>
							</table>
						</form>
					</div> -->
		<?php
				endif;
			endforeach;
		endforeach;
		?>
	<?php else : ?>
		<p>まだグループがありません</p>
	<?php
	endif;
	$stmt = null;
	$pdo = null;
	?>