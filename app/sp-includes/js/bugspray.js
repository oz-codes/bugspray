/**
 * spray issue tracking software
 * Copyright (c) 2009-2010 a2h - http://a2h.uni.cc/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Under section 7b of the GNU General Public License you are
 * required to preserve this notice. Additional attribution may be
 * found in the NOTICES.txt file provided with the Program.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

jQuery.fn.slideFadeOut = function(speed, easing, callback) {
	return this.animate({opacity:'hide',height:'hide'}, speed, easing, callback);
};

function getNewComments()
{
	history.go();
}
	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}
	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) {
					return c.substring(nameEQ.length,c.length);
			}
		}
		return null;
	}
	function eraseCookie(name) {
		createCookie(name,"",-1);
	}
    function scrollTo(theElement){
		var selectedPosX = 0;
		var selectedPosY = 0;
		while(theElement != null){
			selectedPosX += theElement.offsetLeft;
			selectedPosY += theElement.offsetTop;
			theElement = theElement.offsetParent;
		}
	window.scrollTo(selectedPosX,selectedPosY);
	}

jQuery(function($) {
	$("form.config").live("submit",function() {
			$area = $("textarea#comment_form");
			$text = $area.val();
			error = null;
			if($text == "") {
				error = "textarea empty";
			}
			else if($text.match(/^Enter a comment\.\.\.$/i)) {
				error = "did not change textarea";
			}
			else if($text.length < 3) {
				error = "submitted reply is too short";
			}
			if(error != null) {
				alert("There's something wrong with the comment you submitted. Error message is: "+error);
				return false;
			}
		});

	view = readCookie("current");
	if(view == null) {
		createCookie("current","all",1);
		view  = "all";
	}
	$("option:selected").removeAttr("selected");
	$("select[name='status']").children().each(function(k) {
		if($(this).val()==view) {
			$(this).attr("selected","selected");
			$(this).change();
			return false;
		}
	});
        $("select#status").live("change",function() {
            s = $(this).find("option:selected");
            val = $(s).attr("value");
            if(val == 6) {
               $("form#comment_form").append($("<div id='miscdiv'><label for='miscellanea'>ID of duplicate:</label> <input type='text' id='misc' name='misc' /></div>"));
            } else {
               if($("#miscellanea").size() == 0) {
					return true;
				} else {
					$("#miscdiv").remove();
				}
			}
        });
	// drop downs
	$(".drop-button").each(function() {
		var dropbutton = $(this);
		if (dropbutton.get(0).hasAttribute('data-drop'))
		{
			if ($('#' + dropbutton.attr('data-drop')).length)
			{
				// alright, we have a valid target!
				var droptarget = $('#' + dropbutton.attr('data-drop'));
				
				// move the button and target into an element to help with positioning
				var dropposhelper = $('<div style="position: relative;">').insertBefore(dropbutton);
				dropbutton.appendTo(dropposhelper);
				droptarget.appendTo(dropposhelper);
				
				// positioning time! are we dropping to the left or right?
				if (!droptarget.hasClass('drop-right'))
				{
					droptarget.css({
						'position': 'absolute',
						'left': dropbutton.position().left,
						'top': dropbutton.position().top + (dropbutton.innerHeight() - dropbutton.height()) + dropbutton.outerHeight()
					}).hide();
				}
				else
				{
					droptarget.css({
						'position': 'absolute',
						'left': dropbutton.position().left + dropbutton.outerWidth() - droptarget.outerWidth(),
						'top': dropbutton.position().top + (dropbutton.innerHeight() - dropbutton.height()) + dropbutton.outerHeight()
					}).hide();
				}
				
				// the main click event
				dropbutton.click(function(e) {
					if (droptarget.is(':hidden'))
					{
						droptarget.fadeIn(250);
					}
					else
					{
						droptarget.fadeOut(250);
					}
					
					// links used for buttons... well...
					e.preventDefault();
				});
				
				// allow closing by clicking elsewhere
				$("body").click(function(e) {
					if (!$(e.target).closest(".drop-button").length)
					{
						droptarget.fadeOut(250);
					}
				});
			}
		}
	});
	
	// inputs that should clear upon having focus
	$("input.unsel, textarea.unsel").live('focus', function() {
		$(this).removeClass('unsel');
		$(this).attr({'value':''});
	});
	
	// inputs that are lighter before being changed
	$("input.unchanged, textarea.unsel").live('keydown', function() {
		$(this).removeClass('unchanged');
	});
	
	// forms that have a disabled submit button that enable upon change
	$("form.config input[type=text], form.config input[type=password], form.config textarea").live('keydown', function() {
		$(this).closest("form.config").find("input[type=submit]").removeAttr('disabled');
	});
	$("form.config input[type!=text][type!=password], form.config select").live('change', function() {
		$(this).closest("form.config").find("input[type=submit]").removeAttr('disabled');
	});
	
	// ajax form loading image placeholders
	$("#amwnd_btn").prepend(' <img src="img/loading.gif" alt="please wait..." class="loadimg" style="display:none;" />');
	$("form.ajax input[type=submit]").after(' <img src="img/loading.gif" alt="please wait..." class="loadimg" style="display:none;" />');
	
	// everything comments and not more, not less
	if ($("#comment_form").length)
	{
		var caElm = function(elm) {
			return $(elm).closest("article");
		};
		var caId = function(aelm) {
			return $(caElm(aelm)).attr('id').replace(/[^0-9]/g, '');
		};
		
		// comment actions
		$(".comment_quote").click(function() {
			var e = caElm(this);
			// todo - grab bbcode instead of text
                        $box = $("textarea#comment_form");
                        cur = $box.val();
                        cont = $(e).find(".content").text().replace(/^\s*/,"");
                        cont = cont.replace(/\*\*\* Status .+ \*\*\*/g,"").replace(/\s*$/,"\n");
                        username = $(e).find("a.username").text();
			$box.val(cur+'[quote=' + username + ']\n' + cont +'[/quote]');
			scrollTo($box.get(0));
		});
		$(".comment_delete").click(function() {			
			if (confirm('Make sure you want to delete this comment. It cannot be recovered.'))
			{
				var elm = caElm(this);
				$.ajax({
					url: 'manage_issue.php?action=deletecomment&id=' + caId(elm),
					success: function(data) {						
						if (data.success)
						{
							$(elm).slideFadeOut();
						}
					}
				});
			}
		});
	}
	
	// favouriting
	if ($(".ticket .favorite").length)
	{
		$(".ticket .favorite a").click(ticketFavorite);
	}
	
	// filtering tickets
	if ($(".tickets .filter").length)
	{
		$(".tickets .filter select[name=status]").live('change', function() {
			var tickets = $(this).closest(".tickets");
			eraseCookie("current");
			value = $(this).find("option:selected").val()
			createCookie("current",value,1);
			$.ajax({
				type: 'post',
				url: 'ticket_list.php',
				data: {
					type: $(tickets).attr('data-type'),
					status: $(this).find("option:selected").val()
				},
				success: function(data) {
					$(tickets).html($(data).html());
				}
			});
		});
	}
});

function changestatus(id,status,assigns)
{
	// assigns list
	var a;
	for (var i in assigns)
	{
		var selected;
		if (assigns[i][2])
		{
			selected = ' selected="selected"';
		}
		a += '<option value="'+assigns[i][0]+'"'+selected+'>'+assigns[i][1]+'</option>';
	}
	
	// show it	
	$.amwnd({
		title: 'Change status',
		content: 'Hello! You can change the status of this issue here; just select an option.<br /><br />\
		<form>\
			<input type="radio" name="st" value="1" id="st1" /> <label for="st1">Open</label> <br />\
			<input type="radio" name="st" value="2" id="st2" /> <label for="st2">Assigned to</label> <select name="st2a" id="st2a">'+a+'</select><br />\
			<input type="radio" name="st" value="3" id="st3" /> <label for="st3">Resolved</label> <br />\
			<input type="radio" name="st" value="4" id="st4" /> <label for="st4">Postponed</label> <br />\
			<input type="radio" name="st" value="5" id="st5" /> <label for="st5">Declined</label> <br />\
			<br />\
			<div class="ibox_generic">\
				<b>You may enter some additional notes here. These will be posted in the comments. If you do not wish to write any, leave the box blank.</b> (doesn\'t work right now)\
				<br /><br />\
				<textarea cols="60" rows="2" class="mono" id="notes"></textarea>\
			</div>\
		</form>',
		buttons: ['ok','cancel'],
		closer: 'cancel'
	});
	
	// which status is current?
	$("#st"+status).attr({'checked':'checked'});
	
	// and when you click ok...
	$("#amwnd_ok").bind('click', function() {
		$("#amwnd .loadimg").show();
		
		$.ajax({
			type: 'post',
			url: 'manage_issue.php?id=' + id + '&action=status',
			data: $("#amwnd_content form").serialize(),
			dataType: 'json',
			success: function(data) {
				delay = window.location.hostname == '127.0.0.1' || window.location.hostname == 'localhost' ? 250 : 0;
				
				setTimeout(function() {
					$("#amwnd .loadimg").hide();
					
					if (!data.success)
					{
						$.amwnd({
							title: 'Error!',
							content: data.message,
							buttons: ['ok'],
							closer: 'ok'
						});
					}
					else
					{
						history.go();
					}
				}, delay);
			}
		});
	});
}

function confirmurl(title,url,permanent)
{
	var a = '';
	if (permanent)
	{
		a = '<br /><br /><div class="ibox_alert"><img src="img/alert/exclaim.png" alt="" /> <b>This cannot be undone!</b></div>';
	}
	
	$("#amwnd_yes").bind('click',function() {
		location.href = url;
	});
	
	$.amwnd({
		title: title,
		content: 'Are you sure you want to do this?'+a,
		buttons: ['yes','no'],
		closer: 'no'
	});
}

function stringform(title,url,custom)
{
	// form action url
	var a = '';
	if (url)
	{
		a = ' action="'+url+'"';
	}
	
	// extra components
	if (!custom)
	{
		custom = {};
	}
	var extra = '';	
	if (custom.col)
	{
		extra += '<div style="float:left;">Pick a colour:</div><div style="float:left;padding-left:8px;margin-bottom:2px;"><input type="text" value="#7fd4ff" id="amwndcustomcol" name="col" /></div><br />';
	}
	
	// show the actual window
	$.amwnd({
		title:title,
		content:'<form id="st" method="post"'+a+'>'+extra+'<div style="clear:both;">Enter the value: <input type="text" name="str" /></div><input type="hidden" name="sub" /></form>',
		buttons:['ok','cancel'],
		closer:'cancel'
	});
	
	// initialise any custom stuff
	if (custom.col)
	{
		$("#amwndcustomcol").colorPicker();
	}
	
	// and make sure the form submits!
	$("#amwnd_ok").bind('click',function() {
		$("#st").submit();
	});
}

function ticketFavorite()
{
	var orig = $(this);
	var idholder = $(this);
	var ticket = -1;
	
	// scan
	for (i=0; i<15; i++)
	{
		if ($(idholder).attr('data-id') == undefined)
		{
			idholder = $(idholder).parent();
		}
		else
		{
			ticket = $(idholder).attr('data-id');
			break;
		}
	}
	
	if (ticket == -1)
	{
		$.amwnd({
			title: 'Error!',
			content: 'This theme does not have a parent for the favouriting button with the <code>data-id</code> attribute.',
			buttons: ['ok'],
			closer: 'ok'
		});
	}
	else
	{
		$.ajax({
			type: 'post',
			url: 'manage_issue.php?id=' + ticket + '&action=favorite',
			dataType: 'json',
			success: function(data) {
				if (!data.success)
				{
					$.amwnd({
						title: 'Error!',
						content: data.message,
						buttons: ['ok'],
						closer: 'ok'
					});
				}
				else
				{
					// find and replace all instances of on to off and vice versa
					/*! TODO: even though i tried to make this flexible, if you had <button>Starred</button> that wouldn't work... */
					
					var favElm = $(orig).closest(".favorite");
					
					var scanfunc = function() {
						var target = this;
						
						$.each(['class', 'src', 'style', 'value'], function(i, v) {
							var tempattr = $(target).attr(v);
							
							if (tempattr !== undefined)
							{
								if (tempattr.indexOf('-on') != -1)
								{
									$(target).attr(v, tempattr.replace('-on', '-off'));
								}
								else if (tempattr.indexOf('-off') != -1)
								{
									$(target).attr(v, tempattr.replace('-off', '-on'));
								}
								else if (tempattr.indexOf('_on') != -1)
								{
									$(target).attr(v, tempattr.replace('_on', '_off'));
								}
								else if (tempattr.indexOf('_off') != -1)
								{
									$(target).attr(v, tempattr.replace('_off', '_on'));
								}
							}
						});
					};
					
					$(favElm).each(scanfunc);
					$(favElm).find("*").each(scanfunc);
				}
			}
		});
	}
}
