// 管理画面のタブ切り替え処理
document.addEventListener('DOMContentLoaded', handleTabs());

function handleTabs() {
	const tabButtons = document.querySelectorAll('.btn-form-list');
	console.log(tabButtons)
	const tabContents = document.querySelectorAll('.tab-panel');

	// クッキーのハンドリング処理
	let tab = handleCookie(document.cookie);

	// クッキーにタブの保存情報がある場合の処理
	if (tab['tab']) {
		let tabNumber = tab['tab'];

		// タブボタンとタブのis-activeクラスを削除する
		for (let i = 0; i < tabButtons.length; i++) {
			tabButtons[i].classList.remove('is-active');
			tabContents[i].classList.remove('is-active');
		}

		// クッキーから取得したタブボタンをアクティブにする
		let selectedTabButton =　document.querySelector('[data-id='+tabNumber+']');
		console.log(selectedTabButton);
		selectedTabButton.classList.add('is-active');
		
		// クッキーから取得したタブをアクティブにする
		let selectedTabContent = document.getElementById(tabNumber);
		selectedTabContent.classList.add('is-active');
	} 

	// クリックイベントの処理
	for (let i = 0; i < tabButtons.length; i++) {
		tabButtons[i].addEventListener('click', function(event) {
			let tabButton = event.currentTarget;
			let tabContent = document.getElementById(tabButton.dataset.id);

			// すべてのタブボタンのis-activeクラスを削除する
			for (let i = 0; i < tabButtons.length; i++) {
					tabButtons[i].classList.remove('is-active');
					tabContents[i].classList.remove('is-active');
			}
			// クリックしたタブボタンにis-activeクラスを追加する
			tabButton.classList.add('is-active');

			// タブの保存情報を保存しておく
			setCookie('tab', tabButton.dataset.id, 300, 'ogoriai/admin/');

			// タブを表示する
			if(tabContent !== null) {
					tabContent.classList.add('is-active');
			}
		});				
	}
}

// クッキーを設定する
function setCookie(key, value, time, path) {
	let expiresAt = "max-age=" + time;
	document.cookie = key + "=" + value + ";" + expiresAt + ";" + path + ";";
	return document.cookie;
}

// クッキーを連想配列に変換する
function handleCookie(cookie) {
	let array = new Array();
	if (cookie != '') {
		let cookies = cookie.split( '; ' );
		for(let i = 0; i < cookies.length; i++ ) {
			let value = cookies[i].split('=');
		
			// クッキーの名前をキーとして 配列に追加する
			array[value[0]] = decodeURIComponent(value[1]);
		}
	}
	return array;
}

// お知らせ作成タブのセレクトボックス切り替え処理
const select = document.getElementById('recipient');
const input = document.querySelector('.select-recipient');

select.onchange = function() {
	console.log(this.value);
	// 送信先としてグループかユーザーの指定がある場合のみinputを出す
	if (this.value == 1 || this.value == 2) {
		input.style.display = 'block';
	} else {
		input.style.display = 'none';
	}
}
