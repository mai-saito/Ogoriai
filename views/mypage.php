<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';
	require_once MODELS_PATH.'/group.php';
	require_once MODELS_PATH.'/expense.php';

	// セッション開始
	session_start();
	check_session('user_id');
	var_dump($_SESSION);

	?>
	<!DOCTYPE html>
	<html lang="ja">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>マイページ</title>
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
						<li><a href="mypage.php" class="mr-3">マイページ</a></li>
						<li><a href="account.php" class="mr-3">アカウント</a></li>
						<li><a href="../logout.php">ログアウト</a></li>
					</ul>
				</nav>
			</header>
			<main class="mypage">
<?php	
	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// グループと繰越金データの配列を取得する
	$data = initialize_display($pdo, $_SESSION['user_id']);

	// ユーザーが所属しているグループのデータを取得　
	$groups = $data[0];
	if ($groups) :

	// group_idを設定する（セッションにない場合は、所属グループの1番先頭のグループを自動選択）
	if (!isset($_SESSION['group_id'])) {
		$group = reset($groups);
		set_session('group_id', $group['group_id']);
	}
	$group_id = $_SESSION['group_id'];

	// user_groupテーブルからログインしているユーザーの各グループの最新の繰越額を取得する
	$carryover = get_updated_carryover($pdo, $_SESSION['user_id']);

	// ユーザーの各グループに対する最新の繰越金額を取得
	$updated_carryover = $data[1];


?>
			<!-- 左サイドタブメニュー -->
			<ul class="tab-list">
				<li class="btn btn-lg btn-secondary btn-form-list is-active" id="btn-group-list" data-id="tab1">グループ管理</li>
				<li class="btn btn-lg btn-secondary btn-form-list" id="btn-group-form" data-id="tab2">グループ作成</li>
			</ul>
			<!-- 右サイドタブコンテンツ -->
			<section class="tab-content">
				<!-- グループコンテンツ（右サイドタブコンテンツ①） -->
				<div id="tab1" class="tab-panel is-active">
					<!-- グループタブリスト -->
					<div class="group-list-tab">
<?php foreach ($groups as $group): ?>
							<button id="<?php echo $group['group_id'] ?>" class="tab" data-id="group-tab<?php echo $group['group_id'] ?>">
								<img src="../images/avatars/group_avatars/<?php echo $group['group_avatar'] ?>" alt="グループアバター画像" class="rounded-circle">
								<span><?php echo $group['group_name'] ?></span>
							</button>
							<!-- <input type="button" id="<?php echo $group['group_id'] ?>" class="tab" data-id="group-tab<?php echo $group['group_id'] ?>" value="<?php echo $group['group_name'] ?>"> -->
<?php endforeach; ?>
					</div>
					<!-- グループタブコンテンツ -->
					<div class="group-tab-content">
<?php foreach($groups as $group): ?>
						<div id="group-tab<?php echo $group['group_id'] ?>" class="group-tab-panel pt-5 mb-3">
							<div class="container">
								<div class="left-container">
									<div>
										<p><span><?php echo $_SESSION['name']?></span>さんの繰越金額</p>
<?php 
	foreach ($carryover as $value):
		if ($value['group_id'] == $group['group_id']):
			if ($value['carryover'] > 0):
?>
										<p><span class="negative"><?php echo $value['carryover'] ?>円 借り</span></p>
<?php 
			else:
				if ($value['carryover'] < 0):
?>
										<p><span class="positive"><?php echo $value['carryover'] ?>円 貸し</span></p>
<?php
				else:
?>
										<p><span class="positive"><?php echo $value['carryover'] ?>円 </span></p>
<?php
				endif;
			endif;
		endif;
	endforeach;
?>
									</div>
									<ul class="member-list['carryover']">
<?php

	// グループのgroup_nameを取得してセッションに入力する
	$members = get_member_names($pdo, $group['group_id']);

	// グループごとに支出データの入力フォームを作成する
	if ($members) :
		foreach ($members as $member) :
?>
										<li><?php echo $member['name'] ?></li>
<?php
		endforeach;
	endif;
?>
									</ul>
								</div>
								<div class="right-container">
									<!-- グループ管理メニュー -->
									<ul class="panel-menu">
										<li>
											<form action="expense_table.php" method="POST" class="m-0">
												<input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>">
												<input type="submit" value="一覧を表示" class="btn btn-lg btn-primary">
											</form>
										</li>
										<li>
											<form action="settle_up.php" method="POST" class="m-0">
												<input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>">
												<input type="submit" value="清算する" class="btn btn-lg btn-primary">
											</form>
										</li>
<?php 
	$group_leader = get_group_leader($pdo, $group['group_id']);
	if ($group_leader['user_id'] == $_SESSION['user_id']):
?>
										<li>
											<form action="group_account.php" method="POST" class="m-0">
												<input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>">
												<input type="submit" value="グループ管理" class="btn btn-lg btn-primary">
											</form>
										</li>
<?php else: ?>
										<li><input type="submit" value="グループ管理" class="btn btn-lg btn-disabled" disbled></li>
<?php endif; ?>
									</ul>
									<!-- 新しい支出情報の登録フォーム -->
									<form action="../add_expense.php" method="post">
										<input type="text" name="item" id="item" class="form-control mr-3" size="20" placeholder="アイテム">
										<input type="text" name="amount" id="amount" class="form-control mr-3" size="10" placeholder="金額">
										<input type="hidden" name="group_id" value="<?php echo $group['group_id'] ?>">
										<input type="submit" value="入力" class="btn btn-primary">
									</form>
									<!-- 最新5件の支出情報 -->
									<table class="mb-5">
<?php 
	// グループの最新5件の支出情報を表示する
	$expenses = get_first_5_expense_list($pdo, $group['group_id']);
	if(!$expenses): 
?>
										<tr><td>まだ支出データがありません</td></tr>
<?php else: ?>
										<tr><th>名前</th><th>日時</th><th class="amount">金額</th><th class="item">アイテム</th></tr>
<?php	foreach ($expenses as $expense): ?>
<?php $name = (is_null($expense['name'])) ? '退会済みユーザー' : $expense['name']; ?>
										<tr>
											<td><?php echo $name ?></td>
											<td><?php echo $expense['date'] ?></td>
											<td class="amount"><?php echo $expense['amount'] ?></td>
											<td class="item"><?php echo $expense['item'] ?></td>
										</tr>
<?php	endforeach;?>
<?php endif; ?>
									</table>
								</div>
							</div>
						</div>
<?php endforeach; ?>
   				</div>
				</div>	
				<!-- 新規グループ作成コンテンツ（右サイドタブコンテンツ②） -->
				<div id="tab2" class="tab-panel group-form">
					<h1>新しいグループをつくる</h1>
					<p class="ml-2 p-2">
						<span>※ </span>こちらで新しくグループを作成したユーザーがリーダーとなります。<br>
						<span>※ </span>リーダーはグループ管理画面でグループの各種設定が行えます。
					</p>
					<form action="../create_group.php" method="POST" class="p-3">
						<table>
							<tr class="form-group">
								<th><label for="group_name">グループ名：</label></th>
								<td><input type="text" name="group_name" id="group_name" class="form-control" placeholder="新しいグループ名を入力"></td>
							</tr>
							<tr class="form-group">
								<th><label for="rounding">端数の処理方法：</label></th>
								<td>
									<select name="rounding" id="rounding" class="form-control">
										<option value="0"><?php echo '四捨五入' ?></option>
										<option value="1"><?php echo '切り捨て' ?></option>
										<option value="2"><?php echo '切り上げ' ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th class="text-right"><input type="submit" value="メンバーを決める" class="btn btn-lg btn-primary mt-2"></th>
							</tr>
						</table>
					</form>
				</div>
			</section>
<?php else: ?>
		<section class="after-register">
			<ul class="tab-list">
				<li class="btn btn-lg btn-secondary">グループ作成</li>
			</ul>
			<div class="group-form">
				<div class="tab-panel is-shown">
					<h1>新しいグループをつくる</h1>
					<p class="ml-2 p-2">
						<span>※ </span>こちらで新しくグループを作成したユーザーがリーダーとなります。<br>
						<span>※ </span>リーダーはグループ管理画面でグループの各種設定が行えます。
					</p>
					<form action="../create_group.php" method="POST" class="p-3">
						<table>
							<tr class="form-group">
								<th><label for="group_name">グループ名：</label></th>
								<td><input type="text" name="group_name" id="group_name" class="form-control" placeholder="新しいグループ名を入力"></td>
							</tr>
							<tr class="form-group">
								<th><label for="rounding">端数の処理方法：</label></th>
								<td>
									<select name="rounding" id="rounding" class="form-control">
										<option value="0"><?php echo '四捨五入' ?></option>
										<option value="1"><?php echo '切り捨て' ?></option>
										<option value="2"><?php echo '切り上げ' ?></option>
									</select>
								</td>
						</select>
							</tr>
							<tr>
								<th class="text-right" colspan="2"><input type="submit" value="メンバーを決める" class="btn btn-lg btn-primary mt-2"></th>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</section>
<?php endif; ?>
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="../script.js"></script>
</body>

</html>