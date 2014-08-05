<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$includes = false;
$title = false;
$button = false;

if(isset($_COOKIE['LOGOSselected_user'])){

	if(isset($_COOKIE['LOGOSselected_week'])){

		$includes = "./tpl.pickmgt.pick.php";
		$title = "Game Selection";
		$button = true;

	}else{

		$title = "Week Selection";
		$includes = "./tpl.game.week.php";
		$button = true;

	}

}else{

	$title = "User Selection";
	$includes = "./tpl.control.table.php";

}

?>

<div class="fluid">
	<div class="page-row">
		<h1>Game Management</h1>
	</div>
	<div class="page-row">
		<div class="control">
			<?php if($title ==  "Game Selection"): ?>
				<div class="half-block">
					<h5>Manage Pick</h5>
					<br/><br/>
					<input type="hidden" id="mp-gid" name="mp-gid" value="-1" />
					<input type="hidden" id="mp-pid" name="mp-pid" value="-1" />
					<select id="mp-team" name="mp-team">
						<option value="0">Team Select</option>

					</select>
					<label for="mp-team"><i class="icon-tags" title="Team Select"></i></label><br/>

					<input type="text" id="mp-value" name="mp-value" value="Pick Points" />
					<label for="mp-value"><i class="icon-check" title="Pick Points"></i></label><br/>

					<button class="submit" id="pick-update" title="Submit a new pick" >Submit Game</button>
					<button class="submit" id="pick-del" >Delete Pick</button>
					<button class="submit clear">Clear</button><br/>
				</div>
			<?php endif; ?>
			<div class="full-block">

				<h4><?php echo $title;?></h4>
				<?php if($button): ?>
					<button class="submit" onclick="backOffPM()">Back</button><br/>
				<?php endif; ?>
				<br/>
				<div class="scrollme">
					<table id="gamet">

						<?php

						include $includes;

						?>

					</table>
				</div>
				<br/>

			</div>


		</div>

	</div>

</div>