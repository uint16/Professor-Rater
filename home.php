<?php
/*
* Home page. Top rated and worst-rated professors
* Author: Scotty Ward
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/


?>



<div style="margin-left:20px;">
<br/>


<div class="title">Best-rated professors:</div>
<table class="frame"><tr>
<?php
	// Get ratio plank and minimum amount of ratings from the database
	
			// Minimum ratings
	$result=mysql_query("SELECT * FROM options WHERE name='minimum_ratings' ");
	$fetch=mysql_fetch_array($result);
	$minimum_ratings=$fetch['value'];

			// Ratio plank
	$result=mysql_query("SELECT * FROM options WHERE name='ratio_plank' ");
	$fetch=mysql_fetch_array($result);
	$ratio_plank=$fetch['value'];
	
	// Get 5 professors with biggest ratio
	$result=mysql_query("SELECT * FROM professors WHERE ratio>='".$ratio_plank."' AND ratings>='".$minimum_ratings."' ORDER BY ratio DESC LIMIT 5");
	$counter=0;
	while($output=mysql_fetch_array($result)){

		$counter++;
		// Get Professor's profile
		$professor=$fb->api("/".$output['facebook_id'], 'GET');
		?>

		<td valign="top" align="left">
		<?php echo $counter; ?>.
		<div class="small-frame" onclick="window.location='index.php?location=professor&pid=<?php echo $output['facebook_id']?>';">
		<?php echo $professor["name"];?><br />
		<img src="http://graph.facebook.com/<?php echo $professor['id']; ?>/picture?type=normal"/>
		</div>
		</td>

		<?php
	}
	//If no professors are output, display a message
	if ($counter == 0){
		echo "<b><i><u>No professor's match the current criteria.</b></i></u>";
	}

	?>
	</tr></table>
	<br/>

	<div class="title">Worst-rated professors:</div>
	<table class="frame"><tr>
	<?php

	// Get 5 professors with smallest ratio
	$result=mysql_query("SELECT * FROM professors WHERE ratio<'".$ratio_plank."' AND ratings>='".$minimum_ratings."' ORDER BY ratio LIMIT 5");
	$counter=0;
		while($output=mysql_fetch_array($result)){

		$counter++;
		// Get Professor's profile
		$professor=$fb->api("/".$output['facebook_id'], 'GET');
		?>
		<td valign="top" align ="left">
		<?php echo $counter; ?>.
		<div class="small-frame" onclick="window.location='index.php?location=professor&pid=<?php echo $output['facebook_id']?>';">
		<?php echo $professor["name"];?><br />
		<img src="http://graph.facebook.com/<?php echo $professor['id']; ?>/picture?type=normal" />
		</div>
		</td>

		<?php
	}
	//If no professors are output, display a message
	if ($counter == 0){
			echo "<b><i><u>No professor's match the current criteria.</b></i></u>";
	}

	?>
	</tr></table>

	<br/>

	<p align="right"><a class="button" style="margin-right:300px;" href="#" onclick="FB.logout(function(response){window.location.reload();})">Log Out</a></p>
	</div>



