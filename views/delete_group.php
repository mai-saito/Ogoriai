<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';

	// セッション確認
	session_start();
	check_session('group_id');

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// グループのgroup_nameを取得する
	$group = get_group_name($pdo, $_SESSION['group_id']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>グループの削除</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
	<header>
		<h1>おごりあい</h1>
		<nav>
			<ul>
				<li><a href="mypage.php">マイページ</a></li>
				<li><a href="account.php">アカウント</a></li>
				<li><a href="../logout.php">ログアウト</a></li>
			</ul>
		</nav>
	</header>
	<main class="delete-group">
		<p>本当に『<?php echo $group['group_name'] ?>』グループを消去しますか？</p>
		<form action="../delete_group_comp.php" method="POST">
			<table>
				<tr>
					<td><input type="checkbox" name="checkbox" value="checked">グループの削除後は支出データの取得ができなくりますが、よろしいですか？</td>
				</tr>
				<tr>
					<input type="hidden" name="group_id" value="<?php echo $_SESSION['group_id'] ?>">
					<input type="hidden" name="group_name" value="<?php echo $group['group_name'] ?>">
					<td><input type="submit" value="グループを削除する"></td>
				</tr>
			</table>
		</form>
	</main>
</body>
</html>