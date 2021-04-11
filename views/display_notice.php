<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once MODELS_PATH.'/notice.php';

	// セッション開始
	session_start();
	check_session('user_id');

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		// notice_idを取得する
		if (isset($_POST['notice_id'])):
			$notice_id = $_POST['notice_id'];
		else:
			exit('このお知らせは無効です。');
		endif;	

		// お知らせの内容を取得する
		$notice = get_notice_details($pdo, $notice_id, $_SESSION['user_id']);
		if ($notice):
			// 読んだお知らせを既読にする
			update_read_status($pdo, $notice_id, $_SESSION['user_id']); 
?>
<!DOCTYPE html>
	<html lang="ja">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>お知らせ</title>
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/styles.css">
	</head>
	<body>
		<div id="wrapper">
			<header>
				<h1><a href="../index.html"><img src="../images/logo-sm.png" alt="ロゴ画像"></a></h1>
				<nav>
					<ul>
<?php 
	// お知らせがあるか確認する
	$notices = get_notice($pdo, $_SESSION['user_id']);
	if (!$notices):
?>
						<li class="notice-icon"><img src="../images/bell-brown.png" alt="お知らせ" id="notice"></li>
<?php else: ?>
						<li class="notice-icon"><img src="../images/notice-bell-brown.png" alt="お知らせ" id="notice"></li>
<?php endif; ?>
						<li><a href="mypage.php" class="mr-3">マイページ</a></li>
						<li><a href="account.php" class="mr-3">アカウント</a></li>
						<li><a href="../logout.php">ログアウト</a></li>
					</ul>
				</nav>
			</header>
			<main class="notice">
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
				<h1><?php echo $notice['title'] ?></h1>
				<div class="content-container">
					<p class="notice-name m-0 pr-1"><?php echo $notice['name'] ?></p>
					<p class="notice-date pr-1"><?php echo $notice['date'] ?></p>
					<p class="notice-content p-3"><?php echo $notice['content'] ?></p>
				</div>
			</main>
			<footer>
				<div>
					<small>© 2021 Mai Saito.</small>
				</div>
				<div>
					<p><a href="faq.html" class="btn btn-lg btn-contact mr-3">よくあるご質問</a></p>
					<p><a href="contact.php"  class="btn btn-lg btn-contact mr-3">お問合せ</a></p>
					<p><a href="#wrapper"><img src="../images/page-top-nude.png" alt="ページトップへ移動"></a></p>
				</div>
			</footer>
		</div>
		<script src="../script.js"></script>
	</body>
</html>	
<?php 
		endif;
	else:
		$pdo = null;
		exit('直接アクセスは禁止です。');
	endif;
	$pdo = null;
?>