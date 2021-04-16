<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		$user_id = null;
		$group_id = null;
		if ((isset($_POST['group_id']) && strlen($_POST['group_id'])) && (isset($_POST['user_id']) && strlen($_POST['user_id']))):
			// グループIDとユーザーID両方での検索
			$result = get_expense_list($pdo, $_POST['group_id'], $_POST['user_id']);
		else: 
			if ((isset($_POST['group_id']) && strlen($_POST['group_id'])) && !(isset($_POST['user_id']) && strlen($_POST['user_id']))):
			// グループIDのみでの検索
			$result = get_expense_list_by_group($pdo, $_POST['group_id']);
			else: 
				if ((isset($_POST['user_id']) && strlen($_POST['user_id'])) && !(isset($_POST['group_id']) && strlen($_POST['group_id']))):
			// ユーザーIDのみでの検索
			$result = get_expense_list_by_user($pdo, $_POST['user_id']);
				else:
?>
			<p class="insert-alert ml-3">※ユーザーIDかグループIDの入力が必要です。</p>
<?php
				endif;
			endif;
		endif;

		if (isset($result) && $result != false):
			foreach($result as $value):
				// 退会済みのユーザーは「退会済みユーザー」、そのIDは「ー」と表示する
				$member = !is_null($value['name']) ? $value['name'] : '退会済みユーザー';
				$member_id = !is_null($value['user_id']) ? $value['user_id'] : 'ー';
?>
<!-- テーブル入力 -->
							<tr>
								<td><?php echo $value['expense_id'] ?></td>
								<td><?php echo $value['item'] ?></td>
								<td><?php echo $value['amount'] ?></td>
								<td><?php echo $member ?></td>
								<td><?php echo $member_id ?></td>
								<td><?php echo $value['group_name'] ?></td>
								<td><?php echo $value['group_id'] ?></td>
								<td><?php echo $value['date'] ?></td>
							</tr>
<?php endforeach; ?>
<?php else: ?>
							<td colspan="8">データが存在しません。</td>
<?php endif; ?>
<?php
	endif;
	$pdo = null;
?>