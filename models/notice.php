<?php 
	require_once $_SERVER['DOCUMENT_ROOT'].'/ogoriai/models/function.php';
	require_once CONFIG_PATH.'/env.php';

	// DB接続情報
	$pdo = connect_db(DSN, LOCAL_ID, LOCAL_PASSWORD);

	// noticeテーブルに入力する
	function insert_notice($pdo, $title, $content) {
		$stmt = null;
		$sql = 'INSERT INTO notices (title, content, date) VALUES (:title, :content, :date)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':title', $title);
		$stmt -> bindValue(':content', $content);
		$stmt -> bindValue(':date', date("Y-m-d"));
		$stmt -> execute();
		$stmt = null;
	}

	// user_noticeテーブルに入力する
	function insert_user_notice($pdo, $notice_id, $sender_id, $recipient_id) {
		$stmt = null;
		$sql = 'INSERT INTO user_notice (notice_id, sender_id, recipient_id) VALUES (:notice_id, :sender_id, :recipient_id)';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindValue(':notice_id', $notice_id);
		$stmt -> bindValue(':sender_id', $sender_id);
		$stmt -> bindValue(':recipient_id', $recipient_id);	
		$stmt -> execute();
		$stmt = null;
	}

	// 自分へのお知らせを受け取る
	function get_notice($pdo, $recipient_id) {
		$stmt = null;
		$sql = 'SELECT n.notice_id, n.title, n.content, n.date, u_n.sender_id, u_n.recipient_id FROM notices AS n INNER JOIN user_notice AS u_n ON u_n.notice_id = n.notice_id INNER JOIN users AS u ON u.user_id = u_n.sender_id WHERE u_n.recipient_id = :recipient_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':recipient_id', $recipient_id);
		$stmt -> execute();
		return $stmt -> fetchAll(PDO::FETCH_ASSOC);
	}

	// お知らせの内容を受け取る
	function get_notice_details($pdo, $notice_id, $recipient_id) {
		$stmt = null;
		$sql = 'SELECT n.title, n.content, n.date, u.name FROM notices AS n INNER JOIN user_notice AS u_n ON u_n.notice_id = n.notice_id INNER JOIN users AS u ON u.user_id = u_n.sender_id WHERE n.notice_id = :notice_id AND u_n.recipient_id = :recipient_id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':notice_id', $notice_id);
		$stmt -> bindParam(':recipient_id', $recipient_id);
		$stmt -> execute();
		return $stmt -> fetch(PDO::FETCH_ASSOC);
	}
?>