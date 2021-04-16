<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/notice.php';
	
	// セッション開始
	session_start();
	check_admin_session('user_id');	
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
	<link rel="stylesheet" href="../css/styles.css">
</head>

<body>
	<div id="wrapper">
		<header>
			<h1><a href="../index.html"><img src="../../images/logo-sm.png" alt="ロゴ画像"></a></h1>
			<nav>
				<ul>
<?php 

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// ユーザの情報を取得する
	$admin_user = get_user_info($_SESSION['user_id'], $pdo);

	// お知らせがあるか確認する
	$notices = get_notice($pdo, $_SESSION['user_id']);
	if (!$notices):
?>
						<li class="notice-icon"><img src="../../images/bell-brown.png" alt="お知らせ" id="notice"></li>
<?php else: ?>
						<li class="notice-icon"><img src="../../images/notice-bell-brown.png" alt="お知らせ" id="notice"></li>
<?php endif; ?>
					<li class="mr-1"><a href="../../views/account.php"><img src="../../images/avatars/user_avatars/<?php echo $admin_user['user_avatar'] ?>" alt="管理者のアバター" class="avatar rounded-circle"></a></li>
					<li class="mr-3"><a href="../../views/account.php"><span><?php echo $admin_user['name'] ?></span>さん</a></li>
					<li><a href="../admin_logout.php">ログアウト</a></li>
				</ul>
			</nav>
		</header>
		<main class="dashboard">
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
			<h1 class="mb-4">おごりあい管理画面</h1>
			<section>
				<ul class="tab-list">
					<li class="btn btn-lg btn-secondary btn-form-list is-active" data-id="tab1">ユーザーの検索</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab2">グループの検索</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab3">支出データの取得</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab4">管理者の追加</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab5">お知らせの発行</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab6">グループ強制削除</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab7">ユーザー強制削除</li>
				</ul>
				<!-- 切り替え表示用タブ -->
				<div class="tab-content">

					<!-- ユーザー検索タブ -->
					<div id="tab1" class="tab-panel user-search-form is-active">
						<h1 class="mb-4">ユーザーを検索する</h1>
							<form action="dashboard.php" method="POST" class="user-filter-form mb-3">
								<table class="user-filter">
									<tr class="form-group">
										<th><label for="user_id">ユーザーID：</label></th>
										<td><input type="text" name="user_id" id="user_id" class="form-control"></td>
										<th><label for="user-keyword" class="ml-3">キーワード：</label></th>
										<td><input type="text" name="user_keyword" id="user-keyword" class="form-control" size="40" placeholder="名前やメールアドレス"></td>
										<td><input type="checkbox" name="admin" id="admin" class="ml-3 mr-1"></td>
										<th><label for="admin">管理者のみ</label></th>
										<td><input type="submit"value="検索する" class="btn btn-lg btn-primary ml-3"></td>
									</tr>
								</table>
							</form>
							<table class="dashboard-table">
								<tr><th>ユーザーID</th><th>名前</th><th>メールアドレス</th></tr>
<?php include ADMIN_PATH.'/views/user_table.php'; ?>
							</table>
					</div>

					<!-- グループ検索タブ -->
					<div id="tab2" class="tab-panel group-search-form">
						<h1 class="mb-4">グループからユーザーを検索する</h1>
							<form action="dashboard.php" method="POST" class="mb-3">
								<table class="group-filter">
									<tr class="form-group">
										<th><label for="group_id">グループID：</label></th>
										<td><input type="text" name="group_id" id="group_id" class="form-control"></td>
										<th><label for="group-keyword" class="ml-3">グループ名：</label></th>
										<td><input type="text" name="group_keyword" id="group-keyword" class="form-control" size="40" placeholder="グループ名"></td>
										<td><input type="submit"value="検索する" class="btn btn-lg btn-primary ml-3"></td>
									</tr>
								</table>
							</form>
							<table class="dashboard-table">
								<tr><th>ユーザーID</th><th>名前</th><th>メールアドレス</th><th>グループID</th><th>グループ名</th></tr>
<?php include ADMIN_PATH.'/views/group_table.php'; ?>
							</table>
					</div>

					<!-- 支出データ取得タブ -->
					<div id="tab3" class="tab-panel expense-table">
						<h1 class="mb-4">支出データを取得する</h1>
						<!-- 支出項目を選択 -->
						<form action="dashboard.php" method="POST" class="mb-3">
							<table id="table-filter">
								<tr class="form-group">
									<th><label for="group_id">グループID：</label></th>
									<td><input type="text" name="group_id" id="group_id" class="form-control"></td>
									<th><label for="user_id" class="ml-4">ユーザーID：</label></th>
									<td><input type="text" name="user_id" id="user_id" class="form-control"></td>
									<td><input type="submit" value="テーブルを出力する" class="btn btn-lg btn-primary ml-4"></td>
								</tr>
							</table>
						</form>
						<!-- 支出テーブル一覧 -->
						<table class="dashboard-table">
							<tr><th>支出ID</th><th>アイテム</th><th>金額（円）</th><th>支払ったユーザー</th><th>ユーザーID</th><th>グループ名</th><th>グループID</th><th>日付</th></tr>
<?php include ADMIN_PATH.'/views/expense_table.php' ?>
						</table>
					</div>

					<!-- 管理者の追加タブ -->
					<div id="tab4" class="tab-panel add-admin-form">
						<h1 class="mb-4">管理者を追加する</h1>
						<!-- 管理者権限を付与するユーザーを選択 -->
						<form action="dashboard.php" method="POST" class="mb-3">
							<table class="user-filter">
								<tr class="form-group">
									<th><label for="admin-user-id">ユーザーID：</label></th>
									<td><input type="text" name="admin_user_id" id="admin-user-id" class="form-control"></td>
									<td><input type="submit" value="検索する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
						<form action="../add_admin.php" method="POST">
							<table class="dashboard-table">
								<tr><th>ユーザーID</th><th>ユーザー名</th><th colspan="2">メールアドレス</th></tr>
<?php include ADMIN_PATH.'/views/add_admin_table.php'; ?>
							</table>
						</form>
					</div>

					<!-- お知らせの発行タブ -->
					<div id="tab5" class="tab-panel notice-form">
						<h1 class="mb-4">お知らせを発行する</h1>
						<form action="../post.php" method="POST">
							<table>				
								<tr class="form-group">
									<th><label for="title">件名：</label></th>
									<td colspan="2"><input type="text" name="title" id="title" class="form-control mb-3"></td>
								</tr>					
								<tr class="form-group">
									<th><label for="content">内容：</label></th>
									<td colspan="2"><textarea name="content" id="content" rows="10" class="form-control mb-4"></textarea></td>
								</tr>		
								<tr class="form-group">
									<th class="form-group form-row"><label for="recipient">送信先：</label></th>
									<td class="col">
										<select name="recipient" id="recipient" class="form-control">
											<option>選択してください</option>
											<option value="0">全てのユーザー</option>
											<option value="1">指定のグループ</option>
											<option value="2">指定のユーザー</option>
										</select>
									</td>
									<td class="col">
										<input type="text" name="selected_recipient" class="select-recipient form-control" placeholder="グループIDもしくはユーザーIDを入力">
									</td>
								</tr>			
								<tr>
									<input type="hidden" name="name" id="name" value="<?php echo $_SESSION['name'] ?>">
									<th colspan="2"><input type="submit" value="送信する" class="btn btn-lg btn-primary"></th>
								</tr>
							</table>
						</form>
					</div>

					<!-- グループ強制削除タブ -->
					<div id="tab6" class="tab-panel delete-group-form">
						<h1 class="mb-4">グループを強制削除する</h1>
							<!-- 削除するグループを選択 -->
							<form action="dashboard.php" method="POST" class="mb-3">
							<table class="group-filter">
								<tr class="form-group">
									<th><label for="group_id">グループID：</label></th>
									<td><input type="text" name="group_id" id="group_id" class="form-control"></td>
									<td><input type="submit" value="検索する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
						<form action="../delete_group.php" method="POST">
							<table class="dashboard-table">
								<tr><th>グループID</th><th colspan="2">グループ名</th></tr>
<?php include ADMIN_PATH.'/views/delete_group_table.php'; ?>
							</table>
						</form>
					</div>

					<!-- ユーザー強制削除タブ -->
					<div id="tab7" class="tab-panel delete-user-form">
						<h1 class="mb-4">ユーザーを強制削除する</h1>
						<!-- 削除するユーザーを選択 -->
						<form action="dashboard.php" method="POST" class="mb-3">
							<table class="user-filter">
								<tr class="form-group">
									<th><label for="user_id">ユーザーID：</label></th>
									<td><input type="text" name="user_id" id="user_id" class="form-control"></td>
									<td><input type="submit" value="検索する" class="btn btn-lg btn-primary ml-3"></td>
								</tr>
							</table>
						</form>
						<form action="../delete_user.php" method="POST">
							<table class="dashboard-table">
								<tr><th>ユーザーID</th><th>ユーザー名</th><th colspan="2">メールアドレス</th></tr>
<?php include ADMIN_PATH.'/views/delete_user_table.php' ?>
							</table>
						</form>
					</div>
				</div><!-- タブコンテンツ終了 -->
			</section>
		</main>
		<footer>
			<div>
				<small>© 2021 Mai Saito.</small>
			</div>
			<div>
				<p><a href="admin_faq.html" class="btn btn-lg btn-contact mr-3">よくあるご質問</a></p>
				<p><a href="../../views/contact.php"  class="btn btn-lg btn-contact mr-3">お問合せ</a></p>
				<p><a href="#wrapper"><img src="../../images/page-top-nude.png" alt="ページトップへ移動"></a></p>
			</div>
		</footer>
	</div>
	<script src="../js/script.js"></script>
</body>

</html>