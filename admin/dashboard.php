<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/user.php';
	require_once MODELS_PATH.'/expense.php';
	require_once MODELS_PATH.'/group.php';
	
	// セッション開始
	session_start();
	check_session('user_id');	
	var_dump($_SESSION);

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

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
					<li class="mr-1"><a href="../views/account.php"><img src="../images/avatars/user_avatars/<?php echo $admin_user['user_avatar'] ?>" alt="管理者のアバター" class="avatar rounded-circle"></a></li>
					<li class="mr-3"><a href="../views/account.php"><span><?php echo $admin_user['name'] ?></span>さん</a></li>
					<li><a href="admin_logout.php">ログアウト</a></li>
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
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab4">パスワード再発行</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab5">グループ強制削除</li>
					<li class="btn btn-lg btn-secondary btn-form-list" data-id="tab6">ユーザー強制削除</li>
				</ul>
				<!-- 切り替え表示用タブ -->
				<div class="tab-content">
					<!-- 支出データ取得タブ -->
					<div id="tab1" class="tab-panel expense-table is-active">
						<h1>支出データを取得する</h1>
						<!-- 支出項目を選択 -->
						<form action="#" method="POST">
							<table id="table-filter">
								<tr class="form-group">
									<th><label for="group_id">グループID：</label></th>
									<td><input type="text" name="group_id" id="group_id" class="form-control"></td>
									<!-- 下のグループネームは選択されたgroup_idで自動的に入力されるようにする -->
									<td><input type="text" name="group_name" id="group_id" class="form-control group_name" value="<?php echo 'グループネーム' ?>" size="15" readonly></td>　
									<th><label for="user_id">ユーザーID：</label></th>
									<td><input type="text" name="user_id" id="user_id" class="form-control"></td>
									<!-- 下のユーザー名は選択されたuser_idで自動的に入力されるようにする -->
									<td><input type="text" name="name" id="user_id" class="form-control name" value="<?php echo 'ユーザー名' ?>"  size="15" readonly></td>　
									<td><input type="submit" value="テーブルを出力する" class="btn btn-lg btn-primary"></td>
								</tr>
							</table>
						</form>
						<!-- 支出テーブル一覧 -->
					</div>
					<!-- お知らせの発行タブ -->
					<div id="tab2" class="tab-panel notice-form">
						<h1>お知らせを発行する</h1>
						<form action="post.php" method="POST">
							<table>				
								<tr class="form-group">
									<th><label for="title">件名：</label></th>
									<td colspan="3"><input type="text" name="title" id="title" class="form-control mb-3"></td>
								</tr>					
								<tr class="form-group">
									<th><label for="content">内容：</label></th>
									<td colspan="3"><textarea name="content" id="content" rows="10" class="form-control mb-4"></textarea></td>
								</tr>		
								<tr class="form-group">
									<th><label for="recipient">送信先：</label></th>
									<td>
										<select name="recipient" id="recipient">
											<option>選択してください</option>
											<option value="0">全てのユーザー</option>
											<option value="1">指定のグループ</option>
											<option value="2">指定のユーザー</option>
										</select>
									</td>
									<td class="select-recipient ml-3"><input type="text" name="selected_recipient"></td>
								</tr>			
								<tr>
									<input type="hidden" name="name" id="name" value="<?php echo $_SESSION['name'] ?>" class="mb-3">
									<th colspan="2"><input type="submit" value="送信する" class="btn btn-lg btn-primary"></th>
								</tr>
							</table>
						</form>
					</div>
					<!-- 管理者の追加タブ -->
					<div id="tab3" class="tab-panel add-admin-form">
						<h1>管理者を追加する</h1>
						<!-- 管理者権限を付与するユーザーを選択 -->
						<form action="add_admin.php" method="POST">
							<table class="user-filter">
								<tr class="form-group">
									<th><label for="user_id">ユーザーID：</label></th>
									<td><input type="text" name="user_id" id="user_id" class="form-control" value="<?php $user_id ?>" onchange="handleChange(event)"></td>
									<!-- 下のユーザー名とメールアドレスは選択されたuser_idで自動的に入力されるようにする -->
<?php 
	// user_idからユーザーの名前とメールアドレスを取得する
	$user = get_user_info($user_id, $pdo);
?>
									<td><input type="text" name="name" id="user_id" class="form-control name" value="<?php echo $user['name'] ?>" readonly></td>　
									<td><input type="text" name="email" id="user_id" class="form-control email" value="<?php echo $user['email'] ?>" readonly></td>　
									<td><input type="submit" value="管理者に追加する" class="btn btn-lg btn-primary"></td>
								</tr>
							</table>
						</form>
					</div>
					<!-- パスワード再発行タブ -->
					<div id="tab4" class="tab-panel password-form">
						<h1>パスワードを再発行する</h1>
					</div>
					<!-- グループ強制削除タブ -->
					<div id="tab5" class="tab-panel delete-group-form">
						<h1>グループを強制削除する</h1>
							<!-- 削除するグループを選択 -->
							<form action="#" method="POST">
							<table class="group-filter">
								<tr class="form-group">
									<th><label for="group_id">グループID：</label></th>
									<td><input type="text" name="group_id" id="group_id" class="form-control" value="<?php $group_id ?>" onchange="handleChange(event)"></td>
									<!-- 下のグループ名は選択されたgroup_idで自動的に入力されるようにする -->
<?php 
	// group_idからユーザーの名前とメールアドレスを取得する
	$group = get_group_info($pdo, $group_id);
?>
									<td><input type="text" name="group_name" id="user_id" class="form-control group_name" value="<?php echo $group['group_name'] ?>" readonly></td>　　
									<td><input type="submit" value="グループを削除する" class="btn btn-lg btn-danger"></td>
								</tr>
							</table>
						</form>
					</div>
					<!-- ユーザー強制削除タブ -->
					<div id="tab6" class="tab-panel delete-user-form">
						<h1>ユーザーを強制削除する</h1>
						<!-- 削除するユーザーを選択 -->
						<form action="#" method="POST">
							<table class="user-filter">
								<tr class="form-group">
									<th><label for="user_id">ユーザーID：</label></th>
									<td><input type="text" name="user_id" id="user_id" class="form-control" value="<?php $user_id ?>" onchange="handleChange(event)"></td>
									<!-- 下のユーザー名とメールアドレスは選択されたuser_idで自動的に入力されるようにする -->
<?php 
	// user_idからユーザーの名前とメールアドレスを取得する
	$user = get_user_info($user_id, $pdo);
?>
									<td><input type="text" name="name" id="user_id" class="form-control name" value="<?php echo $user['name'] ?>" readonly></td>　
									<td><input type="text" name="email" id="user_id" class="form-control email" value="<?php echo $user['email'] ?>" readonly></td>　
									<td><input type="submit" value="ユーザーを削除する" class="btn btn-lg btn-danger"></td>
								</tr>
							</table>
						</form>
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

	<script src="script.js"></script>
</body>

</html>