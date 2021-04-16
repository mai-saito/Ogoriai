<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		if (isset($_POST['user_id']) && strlen($_POST['user_id'])):
			$user_id = $_POST['user_id'];
			// user_idからユーザーの名前とメールアドレスを取得する
			$user = null;
			$user = get_user_info($user_id, $pdo);
		else: 
?>
				<p class="insert-alert ml-3">※IDの入力が必要です。</p>
<?php 
		endif; 

		// 検索したユーザーをテーブルで表示する
		if (isset($users) && $users != array('')):
?>
								<tr>
									<td><?php echo $user_id ?></td>　
									<td><?php echo $user['name'] ?></td>　
									<td><?php echo $user['email'] ?></td>　
									<input type="hidden" name="name" value="<?php echo $user['name'] ?>">
									<input type="hidden" name="user_id" value="<?php echo $user_id ?>">
									<td class="btn-cell"><input type="submit" value="ユーザーを削除する" class="btn btn-lg btn-danger m-1"></td>
								</tr>
<?php else: ?>
	<td colspan="4">データが存在しません。</td>
<?php endif; ?>		
<?php
	endif;
	$pdo = null;
?>