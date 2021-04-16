<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/group.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// group_idからユーザーの名前とメールアドレスを取得する
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		if (isset($_POST['group_id']) && strlen($_POST['group_id'])):
			$group_id = $_POST['group_id'];

			// group_idからグループ情報を取得する
			$group = null;
			$group = get_group_info($pdo, $group_id);
		else: 
?>
				<p class="insert-alert ml-3">※IDの入力が必要です。</p>
<?php 
		endif; 

		// 検索したグループをテーブルで表示する
		if (isset($group) && $group != false):
?>
								<tr>
									<td><?php echo $group['group_id'] ?></td>　
									<td><?php echo $group['group_name'] ?></td>　
									<input type="hidden" name="group_name" value="<?php echo $group['group_name']  ?>">
									<input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>">
									<td class="btn-cell"><input type="submit" value="グループを削除する" class="btn btn-lg btn-danger m-1"></td>
								</tr>
<?php else: ?>
	<td colspan="3">ユーザーが存在しません。もう一度検索してください。</td>
<?php endif; ?>		
<?php
	endif;
	$pdo = null;
?>