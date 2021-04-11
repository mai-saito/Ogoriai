<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/notice.php';

	// セッション開始
	session_start();
	check_session('user_id');	

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// ユーザー名を取得する
	$sender = get_user_info($_SESSION['user_id'], $pdo);
	$sender_name = $sender['name'];

	if ($_SERVER['REQUEST_METHOD'] === 'POST'):
		// group_idを取得する
		if (isset($_POST['group_id']) && strlen($_POST['group_id'])) {
			$group_id = $_POST['group_id'];
		} else {
			exit('送信エラーです。');
		}

		if (isset($_POST['group_name']) && strlen($_POST['group_name'])) {
			$group_name = $_POST['group_name'];
		} else {
			exit('送信エラーです。');
		}

		// お知らせ内容を指定する
		$title = '清算のお願い';
		$content = <<<EOD
		{$sender_name}さんから{$group_name}への清算のご依頼がございました。<br>
		みなさまの繰越金額は、マイページの各グループ内「清算する」ボタンよりご確認いただけます。<br>
		繰越金額がマイナス表記の方は「貸し」、プラス表記の方は「借り」ている状態ですので、お間違いのないように清算をお願いいたします。<br>
		なお、本アプリ内での金銭の受け渡しはできかねますので、ご了承くださいませ。<br>
		<br>
		おごりあい管理事務局
		EOD;

		// 清算のお知らせを登録する
		insert_notice($pdo, $title, $content);

		// notice_idを変数に保持する
		$notice_id = $pdo->lastInsertId();

		// グループのメンバーを取得する
		$members = get_member_names($pdo, $group_id);
		if ($members){
			foreach($members as $member) {
				if ($member['user_id'] != $_SESSION['user_id']) {
					// 送信者以外のメンバーにお知らせを送る
					insert_user_notice($pdo, $notice_id, $_SESSION['user_id'], $member['user_id']); 
				}
			}
		}
		$pdo = null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>清算の依頼完了</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&family=Noto+Sans&family=Noto+Sans+JP&family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
	<div id="wrapper">
		<header>
			<h1><a href="index.html"><img src="images/logo-sm.png" alt="ロゴ画像"></a></h1>
			<nav>
				<ul>
					<li><a href="views/mypage.php" class="mr-3">マイページ</a></li>
					<li><a href="views/account.php" class="mr-3">アカウント</a></li>
					<li><a href="logout.php" class="mr-3">ログアウト</a></li>
				</ul>
			</nav>
		</header>
		<main class="settle-up">
			<p>清算依頼を承りました。</p>
			<p>他のグループの皆さまにも清算依頼のお知らせを送付済みです。</p>
			<p><a href="views/mypage.php" class="btn btn-lg btn-primary">マイページへ戻る</a></p>
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
<?php
	else:
		$pdo = null;
		exit('直接アクセス禁止です。');
	endif;
?>