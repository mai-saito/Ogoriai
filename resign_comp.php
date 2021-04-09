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
	<div id="wrapper">
		<header>
			<h1><a href="../index.html"><img src="images/logo-sm.png" alt="ロゴ画像"></a></h1>
		</header>
		<main class="comp">
<?php 
	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// 入力エラー配列
	$errors = array();

	// 削除前にユーザーのuser_idを変数として保持しておく
	$user_id = $_SESSION['user_id'];

	// ユーザーの所属するグループのgroup_idを取得する
	$groups = get_groups($pdo, $user_id);

	// 脱退するユーザーの繰越額を取得する
	$remained_carryover = get_updated_carryover($pdo, $user_id);
				
	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		if (!isset($_POST['password']) || !strlen($_POST['password'])):
			$errors['no-password'] = 'パスワードを入力してください。';
		endif;

		// パスワードが合致するか確認する
		$sql = 'SELECT `password` FROM `users` WHERE `user_id` = :user_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':user_id', $user_id);
		$stmt -> execute();
		$result = $stmt -> fetch();
		if ($result): 
			// パスワードを確認する
			if (password_verify($_POST['password'], $result['password'])):
				// usersテーブルからユーザーを削除する
				delete_user($pdo, $user_id);
				
				// user_groupテーブルからもユーザー情報を削除する
				delete_user_group($pdo, $user_id);

				// ユーザーが脱退した後の繰越額を再計算する
				foreach ($groups as $group) {
					foreach ($remained_carryover as $remained) {
						if ($group['group_id'] === $remained['group_id']) {
							recalculate_carryover_after_user_dropped($pdo, $group['group_id'],  $remained['carryover']);
						}
					}
				}
?>
	<p>退会処理が完了いたしました。</p>
	<p><a href="http://localhost/ogoriai/index.html" class="btn btn-lg btn-primary">トップへ戻る</a></p>
<?php
				else:
					$errors['password'] = 'パスワードが異なります。';
					// 入力エラーがあった場合、エラーを表示する
					if (count($errors) != 0):
						notify_errors($errors);
?>
	<input type="submit" value="戻る" onclick="history.go(-1)">
<?php		
					endif;
				endif;	
			endif;
		$stmt = null;	
		else:
			$pdo = null;
			exit('直接アクセス禁止です。');
		endif;
		$pdo = null;
?>	
		</main>
		<footer>
			<div>
				<small>© 2021 Mai Saito.</small>
			</div>
			<div>
				<p><a href="views/faq.html" class="btn btn-lg btn-contact mr-3">よくあるご質問</a></p>
				<p><a href="views/contact.php"  class="btn btn-lg btn-contact mr-3">お問合せ</a></p>
				<p><a href="#wrapper"><img src="images/page-top-nude.png" alt="ページトップへ移動"></a></p>
			</div>
		</footer>
	</div>
</body>
</html>