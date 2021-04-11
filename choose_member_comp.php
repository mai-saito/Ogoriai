<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/notice.php';
	
	// セッション開始
	session_start();
	check_session('user_id');	
	// var_dump($_SESSION);

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// user_groupテーブルに選択されたユーザーを挿入する
		add_member($pdo, $_POST['user_id'], $_SESSION['group_id'], 0); 

		// 各ユーザーの繰越金を再計算する
		recalculate_carryover($pdo, $_SESSION['group_id'], $_SESSION['user_id']); 
	}

	// 現在のメンバーのnameを表示する
	$result = get_member_names($pdo, $_SESSION['group_id']);
	if ($result):
		// group_idからgroup_nameを取得する
		$group = get_group_info($pdo, $_SESSION['group_id']);
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
	<div id="wrapper">
		<header>
			<h1><a href="../index.html"><img src="images/logo-sm.png" alt="ロゴ画像"></a></h1>
			<nav>
				<ul>
<?php 
	// お知らせがあるか確認する
	$notices = get_notice($pdo, $_SESSION['user_id']);
	if (!$notices):
?>
					<li class="notice-icon"><img src="images/bell-brown.png" alt="お知らせ" id="notice"></li>
<?php else: ?>
					<li class="notice-icon"><img src="images/notice-bell-brown.png" alt="お知らせ" id="notice"></li>
<?php endif; ?>
					<li><a href="views/mypage.php" class="mr-3">マイページ</a></li>
					<li><a href="views/account.php" class="mr-3">アカウント</a></li>
					<li><a href="logout.php">ログアウト</a></li>
				</ul>
			</nav>
		</header>
		<main class="choose-member">
			<!-- お知らせセクション -->
			<div class="notice-container">
				<ul class="notice-list">
<?php foreach($notices as $notice): ?>
					<li>
						<form action="display_notice.php" method="POST">
							<input type="hidden" name="title" value="<?php echo $notice['notice_id'] ?>">
							<input type="submit" class="notice-title" value="<?php echo $notice['title'] ?>">
						</form>
					</li>
					<li class="notice-date"><?php echo $notice['date'] ?></li>
<?php endforeach; ?>
				</ul>
			</div>
			<!-- メインセクション -->
			<p>メンバー追加が完了しました。</p>
			<p><span><?php echo $group['group_name'] ?></span>のメンバー</p>
			<table class="member-table mb-3">
<?php foreach ($result as $member => $value): ?>
				<tr>
					<td><?php echo $value['name'] ?></td>
				</tr>
<?php endforeach; ?>
			</table>
			<ul>
				<li class="mr-3"><a href="views/group_account.php" class="btn btn-lg btn-primary">メンバーをさらに追加する</a></li>
				<li class="mr-3">
					<form action="views/group_account.php" method="POST" class="m-0">
						<input type="hidden" name="group_id" value="<?php echo $_SESSION['group_id'] ?>">
						<input type="submit" value="グループ管理に戻る" class="btn btn-lg btn-primary">
					</form>
				</li>
				<li><a href="views/mypage.php" class="btn btn-lg btn-primary">マイページに戻る</a></li>
			</ul>
<?php else: ?>
			<p>メンバーが見つかりません。</p>
<?php endif; ?>
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
<?php 
	$stmt = null;
	$pdo = null;
?>
	<script src="script.js"></script>
</body>
</html>
