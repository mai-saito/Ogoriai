<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/expense.php';

	// セッション確認
	session_start();
	check_session('user_id');

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// ユーザの情報を取得する
	$admin_user = get_user_info($_SESSION['user_id'], $pdo);

	// 入力エラー配列
	$errors = array();

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// group_idが送られているか確認する　
		if (isset($_POST['group_id'])) {
			// 指定のグループをgroupsテーブルから消去する
			delete_group($pdo, $_POST['group_id']);

			// user_groupテーブルから指定のgroup_idの行を削除する
			delete_user_group_by_group($pdo, $_POST['group_id']);
			
			// expensesテーブルから指定のgroup_idの支出行を削除する
			delete_expense_by_group_id($pdo, $_POST['group_id']);
		} else {
			exit('group_idの指定がありません。');
		}
	// 
	}
	$pdo = null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>グループ削除完了</title>
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
		<main class="delete-group">
			<p class="mb-3">グループID：<?php echo $_POST['group_id']?></p>
			<p> グループ名：<?php echo $_POST['group_name'] ?> を削除しました。</p>
			<p><a href="dashboard.php" class="btn btn-lg btn-primary">管理者画面へ戻る</a></p>
		</main>
		<footer>
			<div>
				<small>© 2021 Mai Saito.</small>
			</div>
			<div>
				<p><a href="#"  class="btn btn-lg btn-contact mr-3">お問合せ</a></p>
				<p><a href="#wrapper"><img src="../images/page-top-nude.png" alt="ページトップへ移動"></a></p>
			</div>
		</footer>
	</div>
</body>
</html>