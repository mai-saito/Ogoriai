<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	
	// セッション開始
	session_start();
	check_session('user_id');	
	var_dump($_SESSION);

	// ユーザの情報を取得する
	$admin_user = get_user_info($_SESSION['user_id'], $pdo);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>管理画面</title>
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
					<li>
						<a href="../views/account.php">
							<img src="../images/avatars/user_avatars/<?php echo $admin_user['user_avatar'] ?>" alt="管理者のアバター">
							<span><?php echo $admin_user['name'] ?></span>さん
						</a>
					</li>
					<li><a href="../logout.php">ログアウト</a></li>
				</ul>
			</nav>
		</header>
		<main class="dashboard">
			<h1 class="mb-4">おごりあい管理画面</h1>
			<section>
				<ul class="tab-list">
					<li class="btn btn-lg btn-secondary btn-form-list is-active" data-id="tab1">支出データの取得</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab2">お知らせの発行</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab3">管理者の追加</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab4">パスワードの再発行</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab5">グループの強制削除</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab6">ユーザーの強制退会</li>
				</ul>
				<!-- 切り替え表示用タブ -->
				<div class="tab-content">
					<!-- 支出データ取得タブ -->
					<div id="tab1" class="tab-panel expense-table is-active">
						<h1>支出データを取得する</h1>
					</div>
					<!-- お知らせの発行タブ -->
					<div id="tab2" class="tab-panel notice-form">
						<h1>お知らせを発行する</h1>
					</div>
					<!-- 管理者の追加タブ -->
					<div id="tab3" class="tab-panel add-admin-form">
						<h1>管理者を追加する</h1>
					</div>
					<!-- パスワード再発行タブ -->
					<div id="tab4" class="tab-panel password-form">
						<h1>パスワードを再発行する</h1>
					</div>
					<!-- グループ強制削除タブ -->
					<div id="tab5" class="tab-panel delete-group-form">
						<h1>グループを強制削除する</h1>
					</div>
					<!-- ユーザー強制削除タブ -->
					<div id="tab6" class="tab-panel delete-user-form">
						<h1>ユーザーを強制削除する</h1>
					</div>
				</div>
			</section>
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