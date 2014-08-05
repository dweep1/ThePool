var $default = $("#link-email").val();
var $pageCookieName = 'LOGOSADMINpage';
var passpoints = 0;
var password;
var points = 0;

$(document).ready(function(){
	
	docReady();

});

function docReady(){
	
	if(typeof($.cookie($pageCookieName)) === 'undefined' || $.cookie($pageCookieName) === null){
		
		setPageCookie('tpl.user.php');
	}
	
	setTimeout(function(){
		refreshContentArea();
		setMenuHeader();
	}, 500);

	$(document).on('click', '#generate-link', function () {
		generateLink();
	});
	
	$(document).on('click', '[link-id]', function () {
		resendKey(this);
	});
	
	$(document).on('click', '[ajax]', function () {
		changeContent($(this).attr('ajax'));
		
		if($(this).hasClass("selected")){
			
		}else{
			$(".selected").removeClass("selected");
			$(this).addClass("selected");
		}
		
	});

    $(document).on('click', '[pickrow]', function () {

        loadPick($(this).attr('pick_id'), $(this).attr('game_id'));

        if($(this).hasClass("select")){

        }else{
            $(".select").removeClass("select");
            $(this).addClass("select");
        }

    });
	
	$(document).on('click', '[user]', function () {

        $.cookie('LOGOSselected_user', parseInt($(this).attr('user')), { expires: 7, path: '/' });

        if(getPageCookie() == "tpl.control.php"){
            loadUser($(this).attr('user'));
        }else if(getPageCookie() == "tpl.pickmgt.php"){
            refreshContentArea();
        }
		
		if($(this).hasClass("select")){
			
		}else{
			$(".select").removeClass("select");
			$(this).addClass("select");
		}
	});
	
	$(document).on('click', '[season]', function () {
		
		loadSeason($(this).attr('season'));
		$.cookie('LOGOSselected_season', parseInt($(this).attr('season')), { expires: 7, path: '/' });
		
		if($(this).hasClass("select")){
			
		}else{
			$(".select").removeClass("select");
			$(this).addClass("select");
		}
	});
	
	$(document).on('click', '[seas]', function () {

		$.cookie('LOGOSselected_season', parseInt($(this).attr('seas')), { expires: 7, path: '/' });
		
		setTimeout(function(){
			
			refreshContentArea();
			
		}, 2000);
		
		if($(this).hasClass("select")){
			
		}else{
			$(".select").removeClass("select");
			$(this).addClass("select");
		}
	});
	
	$(document).on('click', '[week]', function () {

		$.cookie('LOGOSselected_week', parseInt($(this).attr('week')), { expires: 30, path: '/' });
		
		setTimeout(function(){
			
			refreshContentArea();
			
		}, 2000);
		
		if($(this).hasClass("select")){
			
		}else{
			$(".select").removeClass("select");
			$(this).addClass("select");
		}
	});
	
	$(document).on('click', '[game]', function () {

		loadGame($(this).attr('game'));
		
		if($(this).hasClass("select")){
			
		}else{
			$(".select").removeClass("select");
			$(this).addClass("select");
		}
	});
	
	$(document).on('click', '#submit-changes', function () {
			
		updateUser();

	});
	
	$(document).on('click', '.clear', function () {
		
		clearInputs();
		
	});
	
	$(document).on('click', '#submit-delete', function () {
		
		var $user_id = $("#uc-user_id").val();
		
		if(parseInt($user_id) == -1){
			
			return false;
			
		}
		
		var result = confirm("Are You Sure you want to delete this account?");
		
		if (result == true){
			deleteUser($user_id);
		}

	});
	
	$(document).on('click', '#season-changes', function () {
		
		updateSeason();

	});
	
	$(document).on('click', '#game-change', function () {
		
		updateGame();

	});
	
	$(document).on('click', '#season-lock', function () {
		
		seasonLock();

	});

    $(document).on('click', '#game-bye', function () {

        byeGame();

    });

    $(document).on('click', '#pick-update', function () {

        submitPick();

    });

    $(document).on('click', '#submit-email', function () {

        submitEmail();

    });
	
	$(document).on("focus blur", "input, textarea", function(event){
	    if (event.type == "focusin") {
	    	if (this.value === this.defaultValue) {
				this.value = '';
			}
	    }else{
	    	if (this.value === '') {
				this.value = this.defaultValue;
			}
	    }
	});	
	
}

function pageLoad(){
	setTimeout(function(){

		if(getPageCookie() == "tpl.season.php"){
			
			var datefield = document.createElement("input");
		    datefield.setAttribute("type", "date");
		    
			if (datefield.type != "date"){ //if browser doesn't support input type="date", initialize date picker widget:
				$("#ms-end").datepicker();
			    $("#ms-start").datepicker();
			}
			
		}
		
		if(getPageCookie() == "tpl.game.php"){
			
			var datefield = document.createElement("input");
		    datefield.setAttribute("type", "date");
		    
			if (datefield.type != "date"){ //if browser doesn't support input type="date", initialize date picker widget:
				$("#mg-date").datepicker();
			}
			
		}
		
		if(getPageCookie() == "tpl.control.php"){
			
			$("#uc-pass").bind('change keyup input',  function(){
		
				passpoints = $(this).val().length;
				password = $(this).val();
		
				var has_letter		= new RegExp("[a-z]");
				var has_caps		= new RegExp("[A-Z]");
				var has_numbers		= new RegExp("[0-9]");
		
				if(has_letter.test(password)) 	{ passpoints += 4; }
				if(has_caps.test(password)) 		{ passpoints += 4; }
				if(has_numbers.test(password)) 	{ passpoints += 4; }
		
				if(passpoints <= 10){
					$(".icon-barcode").addClass("invalid");
				}else if(passpoints > 10){
					$(".icon-barcode").removeClass("invalid");
					$(".icon-barcode").addClass("valid");
				}
				
			});
			
			$("#uc-email").bind('change keyup input',  function(){

        		var length = $(this).val().length;

        		if(length >= 4){ 
        			points++;
        		}else if(length < 4){
        			$(".icon-envelope").addClass("invalid");
        			points = 0;
        		}

        		var value = jQuery.trim($(this).val()).substring(length-1, length);

        		if(value == '@'){
        			points++;
        		}
        		
        		if(value == '.' && points == 2){
        			points++;
        		}

        		if(points >= 3){
        			$(".icon-envelope").removeClass("invalid");
        			$(".icon-envelope").addClass("valid");
        		}

        	});
			
			$("#uc-username").bind('change keyup input',  function(){

        		var length = $(this).val().length;

        		if(length <= 4){
        			$(".icon-user").addClass("invalid");
        		}else if(length > 4){
        			$(".icon-user").removeClass("invalid");
        			$(".icon-user").addClass("valid");
        		}

        	});
			
		}
		
		
		$(".scrollme").mCustomScrollbar({theme: "light"});
	
	}, 500);
	
}

function loadUser($user_id){
	var $submitType = 3;
	
	var $data = {submitType: $submitType, user_id: $user_id};
	
	$.when(ajaxSubmit($data)).done(function(data){

		var $user = jQuery.parseJSON(data);
		
		$("#uc-username").val($user.username);
		$("#uc-email").val($user.email);
		$("#uc-pass").val($("#uc-pass").prop("defaultValue"));
		
		if(parseInt($user.access_level) == 0){
			$('#uc-access').prop('checked', false);
		}else{
			$('#uc-access').prop('checked', true);
		}
		
		if(parseInt($user.user_level) == 0){
			$('#uc-admin').prop('checked', true);
		}else{
			$('#uc-admin').prop('checked', false);
		}
		
		$("#uc-team").val(''+$user.favorite_team_id);
		$("#uc-user_id").val($user_id);
		
		displayFieldMessage("User Loaded");

	});
}

function loadSeason($season_id){
	
	var $submitType = 8;
	
	var $data = {submitType: $submitType, season_id: $season_id};
	
	$.when(ajaxSubmit($data)).done(function(data){

		var $season = jQuery.parseJSON(data);
		
		$("#ms-name").val($season.text_id);
		$("#ms-weeks").val($season.week_count);
		$("#ms-game_count").val($season.game_count);
		$("#ms-id").val($season_id);
		
		$("#ms-start").datepicker("setDate", Date.createFromMysql($season.date_start));
		$("#ms-end").datepicker("setDate", Date.createFromMysql($season.date_end));
		
		displayFieldMessage("Season Loaded");
		
	});
}

function loadGame($game_id){
	
	var $submitType = 13;
	
	var $data = {submitType: $submitType, game_id: $game_id};
	
	$.when(ajaxSubmit($data)).done(function(data){

		var $game = jQuery.parseJSON(data);
		
		$("#mg-id").val($game_id);
		$("#mg-home").val($game.home_team);
		$("#mg-homeScore").val($game.home_score);
		$("#mg-away").val($game.away_team);
		$("#mg-awayScore").val($game.away_score);
		
		$("#mg-date").datepicker("setDate", Date.createFromMysql($game.date));
		
		displayFieldMessage("Game Loaded");
		
	});
	
}

function loadPick($pick_id, $game_id){

    //gets game data

    var $submitType = 18;

    var $data = {submitType: $submitType, game_id: $game_id};
    $team_html = $('#mp-team');

    $.when(ajaxSubmit($data)).done(function(data){

        var $game = jQuery.parseJSON(data);

        $("#mp-gid").val($game_id);

        $team_html.empty();

        $team_html.append($("<option value=\""+$game.away_team+"\">"+$game.away_name+"</option>"));
        $team_html.append($("<option value=\""+$game.home_team+"\">"+$game.home_name+"</option>"));

        displayFieldMessage("Game Loaded");

        if(parseInt($pick_id) == -1)
            return false;

        $submitType = 17;
        $data = {submitType: $submitType, game_id: $game_id, pick_id: $pick_id};

        $.when(ajaxSubmit($data)).done(function(data){

            var $pick = jQuery.parseJSON(data);

            $("#mp-pid").val($pick_id);

            $team_html.val($pick.team_id);

            $("#mp-value").val($pick.value);

            displayFieldMessage("Pick Loaded");

        });

    });

    return true;

}

function submitPick(){

    var $team_id = $('#mp-team').val();
    var $value = $("#mp-value").val();
    var $game_id = $("#mp-gid").val();

    $submitType = 19;

    $data = {submitType: $submitType, game_id: $game_id, team_id: $team_id, value: $value};

    $.when(ajaxSubmit($data)).done(function(data){

        refreshContent('./tpl.pickmgt.pick.php', "#gamet");

        displayFieldMessage("Pick Submitted: "+data);

    });


}

function submitEmail(){

    var $message = $('#email-message').val();
    var $group = $("#email-group").val();

    $submitType = 21;

    $data = {submitType: $submitType, message: $message, group: $group};

    $.when(ajaxSubmit($data)).done(function(data){

        displayFieldMessage("Email Submitted: "+data);

    });


}



function resetStats(){

    var result = confirm("Are You Sure you want to Reset the entire site stats?\nThis may cause a severe lag spike due to the number of processed records");

    if (result == true){
        var $submitType = 20;

        var $data = {submitType: $submitType};

        $.when(ajaxSubmit($data)).done(function(data){

            displayFieldMessage("Season Statis Reset: "+data);

        });
    }

}

function seasonLock(){
	
	var $submitType = 15;
	
	var $season_id = $("#ms-id").val();
	
	if(parseInt($season_id) == -1){
		
		return false;
		
	}
	
	var $data = {submitType: $submitType, season_id: $season_id};
	
	$.when(ajaxSubmit($data)).done(function(data){

		refreshContent('./tpl.season.table.php', "#seasons");
		
		displayFieldMessage("Current Season Changed: "+data);
		
	});
	
}

function backOffGM(){

	if($.cookie('LOGOSselected_week') === null) { 
		
		$.removeCookie('LOGOSselected_season', { path: '/' });
		
	}else{
		
		$.removeCookie('LOGOSselected_week', { path: '/' });

	}

	refreshContentArea();
	clearInputs();
	
}

function backOffPM(){
    if($.cookie('LOGOSselected_week') === null) {

        $.removeCookie('LOGOSselected_user', { path: '/' });

    }else{

        $.removeCookie('LOGOSselected_week', { path: '/' });

    }

    refreshContentArea();
    clearInputs();
}

function updateSeason(){
	
	var $submitType = 9;
	
	var $season_id = $("#ms-id").val();
	
	if(parseInt($season_id) == -1){
		
		$submitType = 10;
		
	}
	
	var $name = $("#ms-name").val();
	var $weeks = $("#ms-weeks").val();
	var $games = $("#ms-game_count").val();
	
	var $date_start = $("#ms-start").datepicker({ dateFormat: 'yy mm dd' }).val();
	var $date_end = $("#ms-end").datepicker({ dateFormat: 'yy mm dd' }).val();
	
	var $data = {submitType: $submitType, season_id: $season_id, name: $name, weeks: $weeks, games: $games, date_start: $date_start, date_end: $date_end};
	
	$.when(ajaxSubmit($data)).done(function(data){

		refreshContent('./tpl.season.table.php', "#seasons");
		
		displayFieldMessage("Season Updated: "+data);
		
	});
	
}

function updateGame(){
	
	var $submitType = 14;
	
	var $game_id = $("#mg-id").val();
	
	if(parseInt($game_id) == -1){
		return false;
	}

	var $home_team =  $("#mg-home").val();
	var $home_score = $("#mg-homeScore").val();
	var $away_team = $("#mg-away").val();
	var $away_score = $("#mg-awayScore").val();
	
	var $date = $("#mg-date").datepicker({ dateFormat: 'yy mm dd' }).val();
	
	var $data = {submitType: $submitType, id: $game_id, home_team: $home_team, home_score: $home_score, away_team: $away_team, away_score: $away_score, date: $date};
	
	$.when(ajaxSubmit($data)).done(function(data){

		refreshContent('./tpl.game.table.php', "#gamet");
		
		displayFieldMessage("Game Updated: "+data);
		
	});
	
}

function byeGame(){

    var $submitType = 16;

    var $game_id = $("#mg-id").val();

    $.when(ajaxSubmit($data)).done(function(data){

        refreshContent('./tpl.game.table.php', "#gamet");

        displayFieldMessage("Game Updated: "+data);

    });

}

function updateUser(){
	
	var $submitType = 4;
	
	var $user_id = $("#uc-user_id").val();
	
	if(parseInt($user_id) == -1){
		
		$submitType = 6;
		
	}
	
	var $username = $("#uc-username").val();
	var $email = $("#uc-email").val();
	var $favorite_team = $("#uc-team").val();
	var $access_level = $('#uc-access').is(":checked");
	var $admin = $('#uc-admin').is(":checked");
	
	var $password = 0;
	
	if($("#uc-pass").val() != $("#uc-pass").prop("defaultValue")){
		
		$password = $("#uc-pass").val();
		
	}
	
	if($submitType == 5 && $password != 0){
		
		return false;
		
	}
	
	if($access_level){
		$access_level = -1;
	}else{
		$access_level = 0;
	}
	
	if($admin){
		$admin = 0;
	}else{
		$admin = -1;
	}
	
	var $data = {submitType: $submitType, user_id: $user_id, username: $username, email: $email, password: $password, favorite_team: $favorite_team, access_level: $access_level, user_level: $admin};
	
	$.when(ajaxSubmit($data)).done(function(data){

		refreshContent('./tpl.control.table.php', "#usercontrol");
		
		displayFieldMessage("User Updated: "+data);
		
	});

}

function deleteUser($user_id){
	
	var $submitType = 7;
	
	var $data = {submitType: $submitType, user_id: $user_id};
	
	$.when(ajaxSubmit($data)).done(function(data){

		refreshContent('./tpl.control.table.php', "#usercontrol");
		
		displayFieldMessage("User Deleted: "+data);
		
	});
	
}

function generateLink(){

	var $link_email = $("#link-email").val();
	var $submitType = 0;
	
	if($default == $link_email){
		$link_email = false;
	}else if($link_email.length < 6){
		$link_email = false;
	}else{
		$submitType = 1;
	}
	
	var $data = {submitType: $submitType, email: $link_email};
	
	$.when(ajaxSubmit($data)).done(function(data){

		$.when(refreshContent('./tpl.user.table.php', "#userkeys")).done(function(){
			$("#generate-reg-link").val(data);
			
			displayFieldMessage("Link Generated, and Email Sent: "+data);
		});
		
	});
	
}

function resendKey($button){
	var $submitType = 2;
	
	var $id = $($button).attr('link-id');
	
	var $data = {submitType: $submitType, link_id: $id};
	
	$.when(ajaxSubmit($data)).done(function(data){

		$.when(refreshContent('./tpl.user.table.php', "#userkeys")).done(function(){
			$("#generate-reg-link").val(data);
			
			displayFieldMessage("Resending Key Complete: "+data);
		});
		
	});
}

Date.createFromMysql = function(mysql_string){ 
	   if(typeof mysql_string === 'string')
	   {
	      var t = mysql_string.split(/[- :]/);

	      //when t[3], t[4] and t[5] are missing they defaults to zero
	      return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);          
	   }

	   return null;   
	}

function refreshContent($templateUrl, $contentArea){
	
	$($contentArea).empty();
	return $($contentArea).load($templateUrl, function() {
		return true;
	});
}

function setMenuHeader(){
	$menu_item = $('[ajax="'+getPageCookie()+'"]');
	
	if($menu_item.hasClass("selected")){
		
	}else{
		$(".selected").removeClass("selected");
		$menu_item.addClass("selected");
	}
}

function refreshContentArea(){
	$.when($("#content").loadContent(getPageCookie())).done(function(){
		pageLoad();
	});
}

function changeContent($templateUrl){
	removePageCookie();
	setPageCookie($templateUrl);
	refreshContentArea();
}

$.fn.loadContent = function($url){
    this.empty();
    return this.load($url, function() {
		return true;
	});
}

function setPageCookie($page){
	$.cookie($pageCookieName, $page, { expires: 30, path: '/' });	
}

function getPageCookie(){
	return $.cookie($pageCookieName);
}

function removePageCookie(){
	return $.removeCookie($pageCookieName, { path: '/' });
}

function displayFieldMessage($message, $timer){
	
	if(typeof($timer)==='undefined') $timer = 6000;

	var div = $('<div class="fieldmessage"></div>');
	
	div.html($message);
	$('#fieldbody').append(div);
	
	var $size = 0-($('#fieldbody').outerHeight());

	div.css({"top": "0"});
	
	setTimeout(function(){
		
		div.css({"top": $size});

		
		setTimeout(function(){
			
			div.remove();

		}, 1300);
		
	}, $timer);
	
}


function resetCookies(){

    //LOGOSADMINpage
    //LOGOScurrent_season
    //LOGOSselected_season
    //LOGOSselected_week

    setPageCookie('tpl.user.php');

    $.removeCookie('LOGOScurrent_season', { path: '/' });
    $.removeCookie('LOGOSselected_season', { path: '/' });
    $.removeCookie('LOGOSselected_week', { path: '/' });
    $.removeCookie('LOGOSselected_user', { path: '/' });

    var $submitType = 12;

    var $data = {submitType: $submitType};

    $.when(ajaxSubmit($data)).done(function(data){

        displayFieldMessage("Cookies Reset: "+data, 10000);
        refreshContentArea();
        clearInputs();
        setMenuHeader();

    });


}

function clearInputs(){
	
	$("input").each(function() {
		$(this).val($(this).prop("defaultValue"));
	});
	
	$("select").each(function(){
		$(this).val($(this).prop("defaultValue"));
	});
	
	$(".select").each(function(){
		$(this).removeClass("select");
	});
	
	$(".invalid").each(function(){
		$(this).removeClass("invalid");
	});
	
	$(".valid").each(function(){
		$(this).removeClass("valid");
	});
	
	$('input[type=checkbox]').attr('checked',false);
	
	displayFieldMessage("Inputs Cleared");
	
}

function ajaxSubmit($data){
	
	return $.ajax({ 
		url: './com/listn.admin.php',
		type: 'post',
		cache: false,
		data: $data,
		success: function(data) {
		    
		    return data;
		    
		}
	});
}