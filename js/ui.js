(function($) {

	/* frameResize */
	$.fn.frameResize = function() {
		var hook = $(this);
		var frameWidth, frameHeight, resizeEvent;
		var asizeWidth = 340;
		var gap = 20;
		var frame = hook.contents().find('.C2Scontent');
		var interval;

		(resizeEvent = function() {
			frameWidth = (document.documentElement.clientWidth < frame.parents('html').attr('scrollWidth')) ? (frame.parents('html').attr('scrollWidth')) + 'px' : '100%';
			frameHeight = frame.outerHeight();
			if($.browser.msie && parseInt($.browser.version) <= 7) {
				var cntWidth = Math.max(document.documentElement.clientWidth, (parseInt(frameWidth) + asizeWidth));
				$('.C2Sfoot, .C2Shead').css({ width: (cntWidth) + 'px' });
			}

			var intervalFN = function() {
					frame = hook.contents().find('.C2Scontent');
					frameHeight = frame.outerHeight();
					if(frameHeight != null || frameHeight > 0) {
						clearTimeout(interval);
						hook.css({ height: frameHeight + 20 + 'px' });
						return;
					}		
			};

			if(frameHeight == null) {
				interval = setTimeout(intervalFN, 100);
			}
			hook.css({
				width: frameWidth,
				height: frameHeight + 20 + 'px'
			});
		})();
		hook.bind('load', resizeEvent);
		$(window).bind('resize', resizeEvent);
	};
	
	/* left navigation */
	$.fn.leftNav = function(num) {
		var hook = $(this).find('a');
		var firstNum = (num) ? num : 0;
		var locationNum = firstNum;

		$.each(hook, function(i) {
			if(hook.eq(i).attr('href').replace('#', '').length > 0 && i != firstNum)	{
				$(hook.eq(i).attr('href')).hide();
			}
			hook.eq(firstNum).parents('li').addClass('on');
		});

		hook.click(function(){
			var objThisNum = hook.index(this);

			if (hook.attr('href').replace('#', '').length > 0) {
				$.each(hook, function(i) {
					$(hook.eq(i).attr('href')).hide();
					$(this).eq(firstNum).parents('li').removeClass('on');
				});
			}
			$(hook.eq(objThisNum).attr('href')).show();
			hook.eq(objThisNum).parents('li').addClass('on');

			locationNum = objThisNum;
			return false;
		});
	};

	/* dropdown navigation */
	/*$.fn.Nav = function() {
 * 		var hook = $(this);
 * 				var locationNum = null;
 * 						
 * 								var navFn = function(num) {
 * 											var lists = hook.eq(num).find('> ul > li');
 * 														$.each(hook, function(i){
 * 																		if(lists.eq(i).children().is('.depth3')) {
 * 																							lists.eq(i).addClass('open');
 * 																											}
 * 																														});
 *
 * 																																	lists.find(' > a').bind('click', function() {
 * 																																						var objThisNum = lists.index($(this).parent());
 * 																																											$.each(lists, function(i) {
 * 																																																	lists.eq(i).removeClass('on');
 * 																																																						});
 *
 * 																																																											if (lists.length > 0 && lists.is('.on') === true || locationNum == objThisNum) {
 * 																																																																	lists.eq(objThisNum).removeClass('on');
 * 																																																																							locationNum = null;	
 * 																																																																													if (lists.eq(objThisNum).children().is('.depth3'))
 * 																																																																																				return false;
 * 																																																																																									} else if (lists.length > 0 && lists.is('.on') === false) {
 * 																																																																																															locationNum = objThisNum;
 * 																																																																																																					lists.eq(objThisNum).addClass('on');
 * 																																																																																																											if (lists.eq(objThisNum).children().is('.depth3')) {
 * 																																																																																																																		return false;	
 * 																																																																																																																							} else {
 * 																																																																																																																												}
 * 																																																																																																																																}			
 * 																																																																																																																																			});
 * 																																																																																																																																					};
 *
 * 																																																																																																																																							$.each(hook, function(i) {
 * 																																																																																																																																										navFn(i);
 * 																																																																																																																																												});
 *
 * 																																																																																																																																													};*/

	/* dropdown navigation */
	$.fn.Nav = function() {
		var hook = $(this);
		var locationNum = null;

		var lists = hook.find('> ul > li');
		$.each(lists, function(i){
			if(lists.eq(i).children().is('.depth3')) {
				lists.eq(i).addClass('open');
			}
		});

		lists.find(' > a').bind('click', function() {
				var objThisNum = lists.index($(this).parent());
				$.each(lists, function(i) {
					lists.eq(i).removeClass('on');
				});

				if (lists.length > 0 && lists.is('.on') === true || locationNum == objThisNum) {
					lists.eq(objThisNum).removeClass('on');
					locationNum = null;	
					if (lists.eq(objThisNum).children().is('.depth3'))
						return false;
				} else if (lists.length > 0 && lists.is('.on') === false) {
					locationNum = objThisNum;
					lists.eq(objThisNum).addClass('on');
					if (lists.eq(objThisNum).children().is('.depth3')) {
						return false;	
				}
			}			
		});
	};
		
	/* tabmenu */
	$.fn.tabMenu = function(num) {
		var hook = $(this).find('a');
		var firstNum = (num) ? num : 0;
		var locationNum = firstNum;

		$.each(hook, function(i) {
			if(hook.eq(i).attr('href').replace('#', '').length > 0 && i != firstNum)	{
				$(hook.eq(i).attr('href')).hide();
			}
			hook.eq(firstNum).addClass('on');
		});

		hook.click(function(){
			var objThisNum = hook.index(this);

			if (hook.attr('href').replace('#', '').length > 0) {
				$.each(hook, function(i) {
					$(hook.eq(i).attr('href')).hide();
					$(this).removeClass('on');
				});
			}
			$(hook.eq(objThisNum).attr('href')).show();
			hook.eq(objThisNum).addClass('on');

			locationNum = objThisNum;
			return false;
		});
	};

	/* navCtrl */
	$.fn.navCtrl = function() {
		var hook = $(this).find('a');
		var obj = $('.C2Swrap');

		hook.click(function(){

				if(obj.is('.C2Snavclose')){
					obj.removeClass('C2Snavclose');
					hook.find('img').attr('src', hook.find('img').attr('src').replace('_open.gif', '_close.gif'));
				} else {
					obj.addClass('C2Snavclose');
					hook.find('img').attr('src', hook.find('img').attr('src').replace('_close.gif', '_open.gif'));
				}
				return false;
		});
	};

	/* tNav */
	$.fn.tNavi = function() {
		var tNav = $('.tNav');
		var tNavPlus = '\<button type=\"button\" class=\"tNavToggle plus\"\>+\<\/button\>';
		var tNavMinus = '\<button type=\"button\" class=\"tNavToggle minus\"\>-\<\/button\>';
		tNav.find('li>ul').css('display','none');
		tNav.find('ul>li:last-child').addClass('last');
		tNav.find('li>ul:hidden').parent('li').prepend(tNavPlus);
		tNav.find('li>ul:visible').parent('li').prepend(tNavMinus);
		tNav.find('li.active').addClass('open').parents('li').addClass('open');
		tNav.find('li.open').parents('li').addClass('open');
		tNav.find('li.open>.tNavToggle').text('-').removeClass('plus').addClass('minus');
		tNav.find('li.open>ul').slideDown(100);
		tNav.find('li.open>ul>li').css('background','none');
		$('.tNav .tNavToggle').click(function(){
			t = $(this);
			t.parent('li').toggleClass('open');
			if(t.parent('li').hasClass('open')){
				t.text('-').removeClass('plus').addClass('minus');
				t.parent('li').find('>ul').slideDown(100);
			} else {
				t.text('+').removeClass('minus').addClass('plus');
				t.parent('li').find('>ul').slideUp(100);
			}
			return false;
		});
		$('.tNav a[href=#]').click(function(){
			t = $(this);
			t.parent('li').toggleClass('open');
			if(t.parent('li').hasClass('open')){
				t.prev('button.tNavToggle').text('-').removeClass('plus').addClass('minus');
				t.parent('li').find('>ul').slideDown(100);
			} else {
				t.prev('button.tNavToggle').text('+').removeClass('minus').addClass('plus');
				t.parent('li').find('>ul').slideUp(100);
			}
			return false;
		});
	};

})(jQuery);
