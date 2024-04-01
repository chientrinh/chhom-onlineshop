jQuery(document).ready(function ($) {

	// ページトップボタン制御
  $(".gotop").hide(); // ページトップボタンを非表示にする
	$(window).on("scroll", function() {

		if ($(window).scrollTop() > 100) { // スクロール位置が100よりも小さい場合に以下の処理をする
			$('.gotop').slideDown("fast"); //  (100より小さい時は)ページトップボタンをスライドダウン
    } else {
			$('.gotop').slideUp("fast"); // それ以外の場合の場合はスライドアップする。
    }

    // フッター固定する
		var scrollHeight = Math.max.apply( null, [document.body.clientHeight , document.body.scrollHeight, document.documentElement.scrollHeight, document.documentElement.clientHeight] ); // ドキュメントの高さ
		scrollPosition = scrollHeight - $(window).scrollTop(); // スクロールした高さ
		browserHeight = $(window).height(); // ブラウザの表示領域（縦）
		footHeight = $("footer").innerHeight(); // フッターの高さ

			if ( (scrollHeight - browserHeight - footHeight - 40 ) >= ( scrollPosition - footHeight) ) {
				$(".gotop").css({ // ".gotop"のpositionをabsoluteに変更し、フッターの高さの位置にする		
					"position":"absolute",
					"bottom": "-" + (scrollHeight - browserHeight - footHeight + 40 ) + "px"
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

$(function(){
	var $setElm = $('#Visual'), //対象にするブロック要素名（IDでも可)
	baseWidth = 1670, //スライドさせるコンテンツ要素の幅
	baseHeight = 480, //スライドさせるコンテンツ要素の高さ

	slideSpeed = 500, //スライドアニメーションのスピード
	delayTime = 5000, //スライドアニメーションの待機時間
	easing = 'linear', //スライドアニメーションのイージング

	autoPlay = '1', // notAutoPlay = '0' 自動スライドON／OFF（ON = 1 , OFF = 0)

	btnOpacity = 0, //左右のNEXT／BACKエリアの透過具合
	pnOpacity = 0;

	var visualSize = $('#Visual ul li').size();
	
	if( visualSize == 1 ){

		$setElm.each(function(){
			var targetObj = $(this);
			var widesliderWidth = baseWidth;
			var widesliderHeight = baseHeight;
			targetObj.children('ul').wrapAll('<div class="wideslider_base"><div class="wideslider_wrap"></div></div>');

			var findBase = targetObj.find('.wideslider_base');
			var findWrap = targetObj.find('.wideslider_wrap');

			var baseListWidth = findWrap.children('ul').children('li').width();
			var baseListCount = findWrap.children('ul').children('li').length;

			var baseWrapWidth = (baseListWidth)*(baseListCount);
			var pagination = $('<div class="pagination"></div>');
		
			var pnPoint = pagination.children('a');
			var pnFirst = pagination.children('a:first');
			var pnLast = pagination.children('a:last');
			var pnCount = pagination.children('a').length;

			var makeClone = findWrap.children('ul');
			makeClone.clone().prependTo(findWrap);
			makeClone.clone().appendTo(findWrap);

			var allListWidth = findWrap.children('ul').children('li').width();
			var allListCount = findWrap.children('ul').children('li').length;

			var allLWrapWidth = (allListWidth)*(allListCount);
			var windowWidth = $(window).width();
			var posAdjust = ((windowWidth)-(baseWidth))/2;

			$(window).bind('resize',function(){
				var windowWidth = $(window).width();
				var posAdjust = ((windowWidth)-(baseWidth))/2;
				findBase.css({left:(posAdjust)});
			}).resize();
			findWrap.css({left:-(baseWrapWidth),width:(allLWrapWidth),height:(baseHeight)});
		});

	} else {

	$setElm.each(function(){
		var targetObj = $(this);
		var widesliderWidth = baseWidth;
		var widesliderHeight = baseHeight;
		targetObj.children('ul').wrapAll('<div class="wideslider_base"><div class="wideslider_wrap"></div><div class="slider_prev"></div><div class="slider_next"></div></div>');

		var findBase = targetObj.find('.wideslider_base');
		var findWrap = targetObj.find('.wideslider_wrap');
		var findPrev = targetObj.find('.slider_prev');
		var findNext = targetObj.find('.slider_next');

		var baseListWidth = findWrap.children('ul').children('li').width();
		var baseListCount = findWrap.children('ul').children('li').length;

		var baseWrapWidth = (baseListWidth)*(baseListCount);

		var pagination = $('<div class="pagination"></div>');
		targetObj.append(pagination);
		

		var pnPoint = pagination.children('a');
		var pnFirst = pagination.children('a:first');
		var pnLast = pagination.children('a:last');
		var pnCount = pagination.children('a').length;
		pnPoint.css({opacity:(pnOpacity)}).hover(function(){
			$(this).stop().animate({opacity:'1'},300);
		}, function(){
			$(this).stop().animate({opacity:(pnOpacity)},300);
		});
		pnFirst.addClass('active');
		pnPoint.click(function(){
			if(autoPlay == '1'){clearInterval(wsSetTimer);}
			var setNum = pnPoint.index(this);
			var moveLeft = ((baseListWidth)*(setNum))+baseWrapWidth;
			findWrap.stop().animate({left: -(moveLeft)},slideSpeed,easing);
			pnPoint.removeClass('active');
			$(this).addClass('active');
			if(autoPlay == '1'){wsTimer();}
		});

		var makeClone = findWrap.children('ul');
		makeClone.clone().prependTo(findWrap);
		makeClone.clone().appendTo(findWrap);

		var allListWidth = findWrap.children('ul').children('li').width();
		var allListCount = findWrap.children('ul').children('li').length;

		var allLWrapWidth = (allListWidth)*(allListCount);
		var windowWidth = $(window).width();
		var posAdjust = ((windowWidth)-(baseWidth))/2;

		findBase.css({left:(posAdjust),width:(baseWidth),height:(baseHeight)});
		findPrev.css({left:-(baseWrapWidth),width:(baseWrapWidth),height:(baseHeight),opacity:(btnOpacity)});
		findNext.css({right:-(baseWrapWidth),width:(baseWrapWidth),height:(baseHeight),opacity:(btnOpacity)});
		$(window).bind('resize',function(){
			var windowWidth = $(window).width();
			var posAdjust = ((windowWidth)-(baseWidth))/2;
			findBase.css({left:(posAdjust)});
			findPrev.css({left:-(posAdjust),width:(posAdjust)});
			findNext.css({right:-(posAdjust),width:(posAdjust)});
		}).resize();

		findWrap.css({left:-(baseWrapWidth),width:(allLWrapWidth),height:(baseHeight)});
		findWrap.children('ul').css({width:(baseWrapWidth),height:(baseHeight)});

		var posResetNext = -(baseWrapWidth)*2;
		var posResetPrev = -(baseWrapWidth)+(baseWidth);

		if(autoPlay == '1'){wsTimer();}

		function wsTimer(){
			wsSetTimer = setInterval(function(){
				findNext.click();
			},delayTime);
		}
		findNext.click(function(){
			findWrap.not(':animated').each(function(){
				if(autoPlay == '1'){clearInterval(wsSetTimer);}
				var posLeft = parseInt($(findWrap).css('left'));
				var moveLeft = ((posLeft)-(baseWidth));
				findWrap.stop().animate({left:(moveLeft)},slideSpeed,easing,function(){
					var adjustLeft = parseInt($(findWrap).css('left'));
					if(adjustLeft == posResetNext){
						findWrap.css({left: -(baseWrapWidth)});
					}
				});
				var pnPointActive = pagination.children('a.active');
				pnPointActive.each(function(){
					var pnIndex = pnPoint.index(this);
					var listCount = pnIndex+1;
					if(pnCount == listCount){
						pnPointActive.removeClass('active');
						pnFirst.addClass('active');
					} else {
						pnPointActive.removeClass('active').next().addClass('active');
					}
				});
				if(autoPlay == '1'){wsTimer();}
			});
		}).hover(function(){
			$(this).stop().animate({opacity:((btnOpacity)+0.1)},100);
		}, function(){
			$(this).stop().animate({opacity:(btnOpacity)},100);
		});

		findPrev.click(function(){
			findWrap.not(':animated').each(function(){
				if(autoPlay == '1'){clearInterval(wsSetTimer);}
				var posLeft = parseInt($(findWrap).css('left'));
				var moveLeft = ((posLeft)+(baseWidth));
				findWrap.stop().animate({left:(moveLeft)},slideSpeed,easing,function(){
					var adjustLeft = parseInt($(findWrap).css('left'));
					var adjustLeftPrev = (posResetNext)+(baseWidth);
					if(adjustLeft == posResetPrev){
						findWrap.css({left: (adjustLeftPrev)});
					}
				});
				var pnPointActive = pagination.children('a.active');
				pnPointActive.each(function(){
					var pnIndex = pnPoint.index(this);
					var listCount = pnIndex+1;
					if(1 == listCount){
						pnPointActive.removeClass('active');
						pnLast.addClass('active');
					} else {
						pnPointActive.removeClass('active').prev().addClass('active');
					}
				});
				if(autoPlay == '1'){wsTimer();}
			});
		}).hover(function(){
			$(this).stop().animate({opacity:((btnOpacity)+0.1)},100);
		}, function(){
			$(this).stop().animate({opacity:(btnOpacity)},100);
		});
	});
	}
});