<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';

	// セッション開始
	session_start();
	check_session('user_id');	
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>退会</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php 
	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// 削除前にユーザーのuser_idを変数として保持しておく
	$user_id = $_SESSION['user_id'];

	// ユーザーの所属するグループのgroup_idを取得する
	$groups = get_group_member($pdo, $user_id);

	// 脱退するユーザーの繰越額を取得する
	$remained_carryover = get_updated_carryover($pdo, $user_id);
				
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		if (!isset($_POST['password']) || !strlen($_POST['password'])):
?>
	<p>パスワードを入力してください。</p>
	<input type="submit" value="戻る" onclick="history.go(-1)">
<?php
			endif;

			// パスワードが合致するか確認する
			$sql = 'SELECT `password` FROM `users` WHERE `user_id` = :user_id';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':user_id', $user_id);
			$stmt -> execute();
			$result = $stmt -> fetch();
			if ($result): 
				// パスワードを確認後、usersテーブルからユーザーを削除する
				if (password_verify($_POST['password'], $result['password'])):
					$stmt = null;
					$sql = 'DELETE FROM `users` WHERE `user_id` = :user_id';
					$stmt = $pdo -> prepare($sql);
					$stmt -> bindParam(':user_id', $user_id);
					$stmt -> execute();

					// user_groupテーブルからもユーザー情報を削除する
					$stmt = null;
					$sql = 'DELETE FROM `user_group` WHERE `user_id` = :user_id';
					$stmt = $pdo -> prepare($sql);
					$stmt -> bindParam(':user_id', $user_id);
					$stmt -> execute();

					// ユーザーが脱退した後の繰越額を再計算する
					foreach ($groups as $group) {
						foreach ($remained_carryover as $remained) {
							if ($group['group_id'] === $remained['group_id']) {
								recalculate_carryover_after_resignation($pdo, $group['group_id'],  $remained['carryover']);
							}
						}
					}
?>
	<p>退会処理が完了いたしました。</p>
	<p><a href="http://localhost/ogoriai/index.html">トップへ戻る</a></p>
<?php
				else:
?>
	<p>パスワードが異なります。</p>
	<input type="submit" value="戻る" onclick="history.go(-1)">
<?php	
				endif;	
			endif;
		$stmt = null;	
		else:
			$pdo = null;
			exit('直接アクセス禁止です。');
		endif;
		$pdo = null;
?>	
</body>
</html>