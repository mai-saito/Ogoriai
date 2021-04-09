<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';

	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);
			
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// user_idがPOSTで送信されているか確認する
		if (isset($_POST['user_id'])) {
			$user_id = $_POST['user_id'];
		} else {
			exit('送信エラーです。');
		}

		// 削除するユーザーの所属するグループのgroup_idを取得する
		$groups = get_groups($pdo, $user_id);

		// 削除するユーザーの繰越額を取得する
		$remained_carryover = get_updated_carryover($pdo, $user_id);

		// ユーザーをusersテーブルから削除する
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
		$stmt = null;
	} else {
			$pdo = null;
			exit('直接アクセス禁止です。');
	}
		$pdo = null;
?>	
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ユーザー削除完了</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
	<div id="wrapper">
		<header>
			<h1><a href="index.html"><img src="../images/logo-sm.png" alt="ロゴ画像"></a></h1>
			<nav>
				<ul>
					<li class="mr-1"><a href="../views/account.php"><img src="../images/avatars/user_avatars/<?php echo $admin_user['user_avatar'] ?>" alt="管理者のアバター" class="avatar rounded-circle"></a></li>
					<li class="mr-3"><a href="../views/account.php"><span><?php echo $admin_user['name'] ?></span>さん</a></li>
					<li><a href="admin_logout.php">ログアウト</a></li>
				</ul>
			</nav>
		</header>
		<main class="delete-user">
			<p class="mb-3">ユーザーID：<?php echo $_POST['user_id']?> ユーザー名：<?php echo $_POST['user_name'] ?>を削除しました。</p>
			<p><a href="dashboard.php" class="btn btn-lg btn-primary">管理者画面へ戻る</a></p>
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