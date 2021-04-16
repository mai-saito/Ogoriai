<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/group.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
	// user_idがPOSTされている場合、user_idからユーザー情報を取得する
	$user_id = null;
	if (isset($_POST['user_id']) && strlen($_POST['user_id'])):
		$user_id = $_POST['user_id'];
		$users = null;
		$users_by_id[] = get_user_info($user_id, $pdo);
		$users = $users_by_id;
	else:
		// 名前もしくはメールアドレスからユーザー情報をあいまい検索する
		$keyword = null;
		if (isset($_POST['user_keyword']) && strlen($_POST['user_keyword'])):
			$keyword = $_POST['user_keyword'];
			$users = null;
			$users = get_user_info_with_keyword($pdo, $keyword); 
		else:
?>
			<p class="insert-alert ml-3">※IDかユーザー名の入力が必要です。</p>
<?php
		endif;
	endif;				

	if (isset($users) && $users != array('')):
	foreach ($users as $user): 
		// 管理者のみの指定がある場合
		if (isset($_POST['admin'])):
			if ($user['admin'] == 1):
?>
								<tr>
									<td><?php echo $user['user_id'] ?></td>
									<td><?php echo $user['name'] ?></td>　
									<td><?php echo $user['email'] ?></td>　
								</tr>
<?php else: ?>
			<p class="insert-alert ml-3">※IDかユーザー名の入力が必要です。</p>
			<td colspan="3">ユーザーが存在しません。もう一度検索してください。</td>
<?php endif; ?>
<?php else: ?>
								<tr>
									<td><?php echo $user['user_id'] ?></td>
									<td><?php echo $user['name'] ?></td>　
									<td><?php echo $user['email'] ?></td>　
								</tr>
<?php endif; ?>
<?php endforeach; ?>
<?php else: ?>
		<td colspan="3">ユーザーが存在しません。もう一度検索してください。</td>
<?php 
		endif; 
	endif;
	$pdo = null;
?>

