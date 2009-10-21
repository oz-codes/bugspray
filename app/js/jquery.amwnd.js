/*
 * a2h's modal window jQuery plugin 0.0.2
 * Copyright (c) 2009 a2h - http://a2h.uni.cc/
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

(function($){

$(document).ready(function() {	
	// add the window
	$("body").prepend('\
		<div id="amwnd" style="display:none;">\
			<div id="amwnd_wrap1"><div id="amwnd_wrap2">\
				<div id="amwnd_wnd">\
					<div id="amwnd_title"></div>\
					<div id="amwnd_content"></div>\
					<div id="amwnd_btn">\
						<button type="button" id="amwnd_cancel">Cancel</button>\
						<button type="button" id="amwnd_ok">OK</button>\
						<button type="button" id="amwnd_no">No</button>\
						<button type="button" id="amwnd_yes">Yes</button>\
					</div>\
				</div>\
			</div></div>\
		</div>'
	);
	
	// add the css - thanks to http://simon.html5.org/sandbox/css/center-fixed for the center fix :D
	$("#amwnd").css({
		'position':'fixed',
		'left':'0px',
		'top':'0px',
		'width':'100%',
		'height':'100%',
		'background':'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlOgljB7jgAAABJJREFUeF4FwIEIAAAAAKD9qY8AAgABdDtSRwAAAABJRU5ErkJggg%3D%3D)',
		'text-align':'center',
		'z-index':'999'
	});
	$("#amwnd_wrap1").css({
		'display':'table',
		'height':'80%',
		'margin':'0px auto',
		'width':$("#amwnd_wnd").css('width'),
		'text-align':'left'
	});
	$("#amwnd_wrap2").css({
		'display':'table-cell',
		'vertical-align':'middle'
	});
	$("#amwnd_wnd").css({
		'-moz-box-shadow':'0px 0px 6px #000',
		'-webkit-box-shadow':'0px 0px 6px #000',
		'box-shadow':'0px 0px 6px #000',
		'background':'#fff',
		'width':'auto !important'
	});
	$("#amwnd_btn button").hide();
});

$.extend({
	amwnd: function(options,elem) {
		settings = $.extend({
			title: '',
			content: '',
			buttons: ['ok'],
			closer: 'ok',
			speed: $.fx.speeds._default
		}, options);
		
		func = function() {
			// set the content
			$("#amwnd_title").html(settings.title);
			$("#amwnd_content").html(settings.content);
			
			// show the appropriate buttons
			for (var i in settings.buttons)
			{
				$("#amwnd_"+settings.buttons[i]).css({'display':'inline'});
			}
			
			// bind the close button as appropriate
			$("#amwnd_"+settings.closer).bind('click',function() {
				for (var i in settings.buttons)
				{
					if (settings.buttons[i] != settings.closer)
					{
						$("#amwnd_"+settings.buttons[i]).unbind();
					}
				}
				closewnd();
			});
			
			// show the window
			$("#amwnd").fadeIn(settings.speed);
		}
		
		closewnd = function() {
			$("#amwnd").fadeOut(settings.speed);
			setTimeout(function(){$("#amwnd_btn button").hide();},settings.speed);
		}
		
		if (elem == undefined)
		{
			func();
		}
		else
		{
			$(elem).bind('click',func);
			return elem;
		}
	}
});

$.fn.extend({
	amwnd: function(options) {
		return this.each(function() {
			$.amwnd(options,this);
		});
	}
});

})(jQuery);