//マイページから選択された支出一覧ページに移動する
function handleSubmit(event) {
	// クリックされたグループのgroup_idをinput:hiddenで送信する
	const selectedForm = event.target.parentElement;
	selectedForm.submit();
}

// クリックイベント処理（マイページタブ切り替え）
function handleClick(event) {
	let cookie = setCookie('group_id', event.target.id, '30');
	window.location.reload();
}

// Cookieを設定する
function setCookie(key, value, time) {
	let expiresAt = "max-age=" + time;
	document.cookie = key + "=" + value + ";" + expiresAt + ";";
	return document.cookie;
}

// マイページのグループタブ切り替え処理
document.addEventListener('DOMContentLoaded', function(){
	// タブに対してクリックイベントを適用
	const tabs = document.querySelectorAll('tab');
	for(let i = 0; i < tabs.length; i++) {
		tabs[i].addEventListener('click', switchTabs);
	}

	// タブ内容の切り替え処理
	function switchTabs(){
		// タブのclassの値を変更
		document.getElementsByClassName('is-active')[0].classList.remove('is-active');
		this.classList.add('is-active');
		// コンテンツのclassの値を変更
		document.getElementsByClassName('is-show')[0].classList.remove('is-show');
		const arrayTabs = Array.prototype.slice.call(tabs);
		const index = arrayTabs.indexOf(this);
		document.getElementsByClassName('panel')[index].classList.add('is-show');
	};
});

// expense_tableのモーダル操作
const modalButtons = document.querySelectorAll('.expense-table #modal-button');
const modals = document.querySelectorAll('.expense-table #setting-modal');
const close = document.querySelectorAll('.expense-table .modal .close');

// モーダルを開く
modalButtons.forEach(function (modalButton) {
	modals.forEach(function (modal) {
		modalButton.onclick = function() {
			if (modal.style.display === 'block') {
				modal.style.display = 'none';
			} else {
				modal.style.display = 'block';
			}
		}
	});
});

// モーダルを閉じる
close.forEach (function (button) {
	modals.forEach(function (modal) {
		button.onclick = function () {
			modal.style.display = 'none';
		}
	});
});

modals.forEach(function(modal) {
	modal.onclick = function() {
		if (modal.style.display === 'block') {
			modal.style.display = 'none';
		}
	}
});




