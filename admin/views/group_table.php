<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/group.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// グループ情報で検索する
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		// グループのメンバーの名前とuser_idを取得する
		$group_id = null;
		if (isset($_POST['group_id']) && strlen($_POST['group_id'])):
			$users_in_group = null;
			$users_in_group = get_member_names($pdo, $_POST['group_id']);
		else:
			// グループ名からあいまい検索をかける
			$keyword = null;
			if (isset($_POST['group_keyword']) && strlen($_POST['group_keyword'])):
				$keyword = $_POST['group_keyword'];
				$users_in_group = null;
				$users_in_group = get_member_names_with_keyword($pdo, $keyword);
			else:
				?>
							<p class="insert-alert ml-3">※IDかグループ名の入力が必要です。</p>
				<?php
			endif;
		endif;
		if (isset($users_in_group) && $users_in_group != false):
		 foreach ($users_in_group as $user_in_group): 

			// グループIDが取得できない場合（IDでの検索時）
			$group_id = isset($user_in_group['group_id']) ? $user_in_group['group_id'] : $_POST['group_id']; 

				// グループIDが取得できない場合（IDでの検索時）
			$selected_group_name = get_group_info($pdo, $group_id);
			$group_name = isset($user_in_group['group_name']) ? $user_in_group['group_name'] : $selected_group_name['group_name'];
?>
								<tr>
									<td><?php echo $user_in_group['user_id'] ?></td>
									<td><?php echo $user_in_group['name'] ?></td>　
									<td><?php echo $user_in_group['email'] ?></td>　
									<td><?php echo $group_id ?></td>　
									<td><?php echo $group_name ?></td>　
								</tr>
<?php endforeach; ?>
<?php else: ?>
	<td colspan="5">ユーザーが存在しません。もう一度検索してください。</td>
<?php endif; ?>
<?php
	endif;
	$pdo = null;
?>
