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

	if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		// group_idを取得する
		if (isset($_POST['group_id']) && strlen($_POST['group_id'])) {
			$group_id = $_POST['group_id'];
		} else {
			exit('送信エラーです。');
		}

		// お知らせ内容を指定する
		$title = '清算のお願い';
		$content = <<<EOD
		{$sender_name}さんから{$_SESSION['group_name']}への清算のご依頼がございました。<br>
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
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/ogoriai/views/settle-up.php');
	}	else {
		$pdo = null;
		exit('直接アクセス禁止です。');
	}

?>