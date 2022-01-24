<!DOCTYPE html>
<html>

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>Link Grabber</title>
<style type="text/css">
.auto-style1 {
	margin-bottom: 0px;
}
.auto-style2 {
	font-size: large;
}
</style>
<script type="text/javascript">

function target_popup(form) {
    window.open('', 'formpopup', 'width=500,height=600,resizeable,scrollbars');
    form.target = 'formpopup';
}
</script>

</head>

<body>

<h1 class="auto-style2">TurfWars Player Finder/Grabber v0.64</h1>


<form onsubmit="target_popup(this)" method="post" action="get_player.php">

	<table style="width:100%;">
			<tr>		
		</tr>
		<tr>
			<td>Access Token <input type="text" id="COOKIE" name="COOKIE" style="width:250px;" value="">
			</td>
		</tr>
		<tr>
			<td>Enter Last Good: <input type="text" id="PLAYER_NO_START" name="PLAYER_NO_START" style="width:70px;" value="5473"> 
			Requests: <input type="text" id="TIMES_BY" name="TIMES_BY" style="width:50px;" value="25">
			End: <input type="text" id="PLAYER_NO_FINISH" name="PLAYER_NO_FINISH" style="width:70px;" value="">
			</td>			
		</tr>
		<tr>
			<td>&nbsp;</td>			
		</tr>		
		<tr>
			<td><input name="Submit" type="submit" value="Go get 'em tiger!" /></td>			
		</tr>

		
	</table>

</form>

<script>
document.getElementById("PLAYER_NO_FINISH").onclick = function() {myFunction()};

function myFunction() {
  
	//here we need to pick 
	let PLAYER_NO_START = document.getElementById("PLAYER_NO_START").value;
	let TIMES_BY = document.getElementById("TIMES_BY").value;
  
	let PLAYER_NO_FINISH = (32 * +TIMES_BY) + +PLAYER_NO_START;

	document.getElementById("PLAYER_NO_FINISH").value = PLAYER_NO_FINISH;
  
	//alert(PLAYER_NO_FINISH);
  
	//document.getElementById("demo").innerHTML = "YOU CLICKED ME!";
}
</script>

</body>

</html>
