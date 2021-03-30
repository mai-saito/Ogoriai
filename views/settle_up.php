<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';
	
	// セッション確認
	session_start();
	check_session('user_id');

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// グループメンバーの名前を取得する
	$members = get_member_names($pdo, $_SESSION['group_id']);

	// グループ全体の合計金額を出す
	$group_total = get_group_total($pdo, $_SESSION['group_id']);

	// user_groupテーブルから各メンバーの現在の繰越額を取得する
	$carryovers = get_carryover($pdo, $_SESSION['group_id']);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>清算</title>
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
	<main class="settle-up">
		<table>			
			<tr><th>名前</th><th>支払った金額</th><th>繰越している金額</th></tr>
<?php 
	foreach ($members as $member):
		foreach ($carryovers as $carryover):
			if ($member['user_id'] === $carryover['user_id']):
				$member_subtotal = is_null(get_user_total($pdo, $_SESSION['group_id'], $member['user_id'])) ? $member_subtotal['member_subtotal'] : 0;
?>
			<tr>
				<td><?php echo $member['name'] ?></td>
				<td><?php echo $member_subtotal?>円</td>
				<td><?php echo $carryover['carryover'] ?>円</td>
			</tr>
<?php 
			endif;
		endforeach;
	endforeach; 
?>
</body>
</html>
