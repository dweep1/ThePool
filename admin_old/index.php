<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>NFL The Pool</title>

<style type="text/css">
@import url(http://fonts.googleapis.com/css?family=Open+Sans:400,300);

HTML, BODY{
	margin:0;
	padding:0;
	font-size:12px;
	font-family: 'Open Sans', sans-serif;
	letter-spacing:1px;
	background: #2f2f2f;
	color: #fff;
	font-weight:300;
}

.container{
	height:auto;
	width:250px;
	background: rgba(0,0,0,0.5);
	padding:15px;
	margin:15% auto;
	overflow:hidden;
	
	-webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.7) inset;
	-ms-box-shadow: 0px 0px 3px rgba(0,0,0,0.7) inset;
	box-shadow: 0px 0px 3px rgba(0,0,0,0.7) inset;
	
}

H1,H2,H3,H4,H5,H6{
	font-family: 'Open Sans', sans-serif;
	font-weight:300;
	
}

H1{
	font-size:48px;
	text-align:center;
	line-height:48px;
	padding:0;
	margin:0;
	margin-bottom:10px;
	color: rgba(212,212,212,1);
}

H2{
	font-size:28px;
	text-align:center;
	line-height:48px;
	padding:0;
	margin:0;
	margin-bottom:10px;
	color: rgba(212,212,212,1);
}

.right{
	float:right;
}

.left{
	float:left;
}

.error {
    text-align: center;
    font-size: 10px;
    line-height: 10px;
    font-weight: 500;
    width: 250px;
    height: 0px;
    padding:5px 0px;
    background: rgba(255, 0, 0, 0.3);
    margin: 0 auto 0;
    border-radius: 3px;
    color: rgba(255, 255, 255, 0.9);
  
    -webkit-transition: height 0.2s ease;
    -ms-transition: height 0.2s ease;
    transition: height 0.2s ease;
}

input, textarea {
    font-family: 'Open Sans', sans-serif;
    font-size:16px;
    font-weight:300;
    width: 236px;
    color: rgb(131,131,131);
    height: auto;
    padding: 7px;
    border:none;
    border-bottom: solid 1px rgba(255,255,255,0.1);
    outline: 0 none;
    margin-bottom: 15px;
    background: none;
}

#forgot-password{
	display:none;
}

.ui-submit{
	background: rgba(255, 20, 20, 0.6);
	outline: 0 none;
	border:none;
	padding:10px 15px;
	font-family: 'Open Sans', sans-serif;
    font-size:14px;
	font-weight:300;
	color: #fff;
	
	-webkit-transition: background-color 0.2s ease;
	-ms-transition: background-color 0.2s ease;
	transition: background-color 0.2s ease;
}

.ui-submit:hover{
	background: rgba(255, 0, 0, 0.5);
}

.ui-submit:active{
	background: rgba(255, 0, 0, 0.3);
}

</style>

</head>
<body>
    <section id="login-con" class="container">
    
        <h1>Login</h1>

        <div class="error"></div>
        <br />
        
        <input type="text" name="username" id="username" value="Username" />
        <br /><br />
        <input type="text" name="pass" id="pass" value="Password" />
        <br /><br />
        
        <button onclick="swap(1)" class="ui-submit">Forgot Password</button>
        <button id="login" class="ui-submit right">Submit</button>
        

    </section> 
    
    <section id="forgot-password" class="container">
    
        <h2>Forgot Password</h2>
        
        <div class="error"></div>
        <br />
        
        <input type="text" name="fusername" id="fusername" value="Username" />
        <br /><br />
        <input type="text" name="fpass" id="fpass" value="Email" />
        <br /><br />
        
        <button onclick="swap(0)" class="ui-submit">Back</button>
        <button id="forgot" class="ui-submit right">Submit</button>
        
    </section>
    
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

<script>
$(document).ready(function(){
	
	resize();

	$("#login").live('click', function () {
		logmein();
	});
	
	$(window).resize(function () { 
		resize();
    });

	$("#pass").focus(function() {
	      if (this.value === this.defaultValue) {
	          this.value = '';
				this.type = 'password';
	      }
	}).blur(function() {
	      if (this.value === '') {
	          this.value = this.defaultValue;
				this.type = 'text';
	      }
	});

	$("#username")
	.focus(function() {
		if (this.value === this.defaultValue) {
			this.value = '';
		}
	}).blur(function() {
		if (this.value === '') {
			this.value = this.defaultValue;
		}
	});
});

function resize(){
    var $resize = $(".container");
	
	var $top = ($(window).height()/2)-160;

	if($top < 20){
	    $top = 20;
	}

	$resize.css({"margin-top": $top});
}

function swap($animation){
	if($animation === 1){
		$("#login-con").css({"display": "none"});
		$("#forgot-password").css({"display": "block"});
	}else{
		$("#login-con").css({"display": "block"});
		$("#forgot-password").css({"display": "none"});
	}
}

function showResult($result){
	$(".error").css({"height": "auto"});
	$(".error").multiline($result);
	
	setTimeout(function () {
		
		clearResult();
  	
	}, 10000);
}

function clearResult(){
	$(".error").css({"height": "0px"});
	$(".error").empty();
}

$.fn.multiline = function(text){
    this.text(text);
    this.html(this.html().replace(/\n/g,'<br/>'));
    return this;
}

function logmein(){
	var $submitType = 0;
	var $username = $("#username").val();
	var $pass = $("#pass").val();
	
	$.ajax({ url: './com/listn.adminlogin.php',
		  type: 'post',
		  cache: false,
		  data: {submitType: $submitType, username: $username, pass: $pass},
		  success: function(data) {
			  
			  if(checkResponse(data)){
				  showResult("Logging In...");
			  }else{
				  showResult(data);
			  }
			  
		  }
	});
}

function checkResponse(response){

	var length = 9;
	var restest = response.substring(0,length);

	if(restest == "Location:"){
		var location = response.substring(length,response.length);

		window.location = ''+location;
		return true;
	}

	return false;

}

</script>
     
</body>
</html>