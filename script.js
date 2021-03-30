
// mypageから選択されたexpense_tableに移動する
function handleClick(event) {
	const selectedForm = event.target.parentElement.parentElement;
	selectedForm.submit();
}

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




