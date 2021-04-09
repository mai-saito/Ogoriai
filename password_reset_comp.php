<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// エラー入力用配列
	$errors = array();

	if ($_SERVER['REQUEST_METHOD'] === 'POST'):

		// メールの宛先を指定する
		$email = null;
		if (!isset($_POST['email']) || !strlen($_POST['email'])) {
			$errors['email'] = 'メールアドレスを入力してください。';
		} else if (strlen($_POST['email']) > 50) {
			$errors['email'] = 'メールアドレスは50文字以下で入力してください。';
		}	else if (!preg_match('/^[a-zA-Z0-9]+[a-zA-Z0-9-_.]*@[a-zA-Z0-9-_]+.[a-zA-Z0-9-_.]+$/', $_POST['email'])) {
			$errors['email'] = 'そのメールアドレスは無効です。';
		} else {
			$email = $_POST['email'];
		}

		if (count($errors) != 0) {
			// エラーを出力する
			notify_errors($errors);
		} else {
			// メールアドレスの登録があるか確認する
			// $result = check_email($pdo, $email);
			// if ($result) {
				// URL用の文字列を作成する
				$passwordResetToken = md5(uniqid(rand(), true));

				// メール件名の作成
				$subject = 'パスワードの再設定について（おごりあい）';

				// メール本文の作成
				$message = "ーーーーーーーーーー\n";
				$message .= "こちらのメールは自動送信です。返信はできかねますので、ご了承ください。\n";
				$message .= "ーーーーーーーーーー\n"; 
				$message .= "いつもおごりあいをご利用いただき、誠にありがとうございます。\n";
				$message .= "パスワードの再設定は、下記URLをクリックしてください。\n";
				$message .= "http://".$_SERVER['HTTP_HOST']."/ogoriai/password_reset.php?passwordReset=".$passwordResetToken."\n\n";
				$message .= "おごりあい　管理事務局";

				// メールヘッダーの作成
				$header = "MIME-Version: 1.0\n";
				$header .= "From: おごりあい <noreply@ogoriai.com>\n";

				// メールの送信
				$is_sent = send_email($email, $subject, $message, $header);
				if ($is_sent) {
					$stmt = null;

					// メールを送信した日付と時刻を確認する
					date_default_timezone_set('Asia/Tokyo');
					$date  = date("Y/m/d H:i:s");
					// password_resetテーブルに$passwordResetTokenとuser_id, dateを保存する
				}
			}
		// }
?>
<!-- メールアドレスが登録されてるか判別されないためにも、送信できない場合も「送信した」画面を出す -->
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>メール送信画面</title>
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
					<li><a href="views/login.html" class="mr-3">ログイン</a></li>
					<li><a href="views/register.html">登録</a></li>
				</ul>
			</nav>
		</header>
		<main class="password-reset">
			<section>
				<p>パスワード再設定のメールを送信いたしました。</p>
				<p>メールが届くまで、もう少々お待ちくださいませ。</p>
			</section>
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
	$pdo = null;
?>

					
