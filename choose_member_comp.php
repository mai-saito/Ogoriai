<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/expense.php';
	
	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// user_groupテーブルに選択されたユーザーを挿入する
		add_member($pdo, $_POST['user_id'], $_SESSION['group_id']); 

		// 各ユーザーの繰越金を再計算する
		recalculate_carryover($pdo, $_SESSION['group_id'], $_SESSION['user_id']); 
	}

	// 現在のメンバーのnameを表示する
	$result = get_member_names($pdo, $_SESSION['group_id']);
	if ($result):
		// group_idからgroup_nameを取得する
		$group = get_group_name($pdo, $_SESSION['group_id']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>メンバー選択完了</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
	<header>
		<h1><a href="index.html">おごりあい</a></h1>
		<nav>
			<ul>
				<li><a href="views/mypage.php">マイページ</a></li>
				<li><a href="views/account.php">アカウント</a></li>
				<li><a href="logout.php">ログアウト</a></li>
			</ul>
		</nav>
	</header>
	<main class="choose_member">
		<h1>メンバー追加が完了しました。</h1>
		<p>『<?php echo $group['group_name'] ?>』のメンバー</p>
		<ul>
<?php foreach ($result as $member => $value): ?>
			<li><?php echo $value['name'] ?></li>
<?php endforeach; ?>
		</ul>
		<p><a href="views/choose_member.php">メンバーをさらに追加する</a></p>
		<p><a href="views/mypage.php">マイページに戻る</a></p>
<?php else: ?>
		<p>メンバーが見つかりません。</p>
<?php endif; ?>
	</main>
<?php 
	$stmt = null;
	$pdo = null;
?>
</body>
</html>
