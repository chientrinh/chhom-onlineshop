jQuery(document).ready(function ($) {

	// ページトップボタン制御
	
  $(".gotop").hide(); // ページトップボタンを非表示にする
	$(window).on("scroll", function() {

		if ($(this).scrollTop() > 100) { // スクロール位置が100よりも小さい場合に以下の処理をする
			$('.gotop').slideDown("fast"); //  (100より小さい時は)ページトップボタンをスライドダウン
    } else {
      $('.gotop').slideUp("fast"); // それ以外の場合の場合はスライドアップする。
    }

    // フッター固定する
		scrollHeight = $(document).height(); 	// ドキュメントの高さ
		scrollPosition = $(window).height() + $(window).scrollTop(); // ウィンドウの高さ+スクロールした高さ→現在のトップからの位置
		footHeight = $("footer").innerHeight(); // フッターの高さ
		if ( scrollHeight - scrollPosition  <= 215 ) {
			$(".gotop").css({ // ".gotop"のpositionをabsoluteに変更し、フッターの高さの位置にする		
				"position":"absolute",
				"bottom": "215px"
			});
		} else { // それ以外の場合は元のcssスタイルを指定
			$(".gotop").css({
				"position":"fixed",
				"bottom": "0px"
			});
		}
	});

	// ページトップへスムーススクロール
	$('.gotop a').click(function () {
		$('body,html').animate({
		scrollTop: 0
	    }, 500); // スクロールスピード
	    return false;
	 });

});