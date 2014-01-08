
<div style="margin-left:20px;">
<?php
/*
* Search Page
* Author: Scott Ward
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/
	include 'mysql.php';
	$searched = $_REQUEST['search'];
	$id = 0;
	$uid = mysql_query(" SELECT * FROM professors"); 
	$schoolID = mysql_query(" SELECT * FROM schools");
	$notFoundP = true;
	$notFoundS = true;
	$count = 0;
	$searchResultsArray = array();
	$tempArray = array();
	
	// loops through users table in database to see if $searched is a name in our table.
	$arraySearchTerms = explode (" ", $searched);
	$searchTermsLength = sizeof($arraySearchTerms);
	
	if (empty($searched)){
		echo '<table align="center"><tr><td>Your search " "  was invalid.</td></tr></table>';
	} else {
		echo "<b> Professors: </b><br/>";
		// Prints out a table of professors 5 per row
		?><table><tr><?php
			
				/* ~~~~~~~~~~ Professors ~~~~~~~~~~ */
				$counter = 0;
			?></tr><tr width = "900px"><?php
				while ($output = mysql_fetch_array($uid)){
					if ($counter == 5){
						echo '</tr>';
						echo '<tr>';
						$counter = 0;
					}
					$fbid = $output['facebook_id'];
					$user = $fb->api($fbid);
					// compares name from facebook id to searched string
					if (stristr($user['name'],$searched)){
						//Display results if there is a substring that matches the searches string
						?><td width = 180px height = 200px align="left" valign="top"><?php
						
							echo '<div id="searchResult">';
							echo '<div class="searchImage" style="background-image:url(\'http://graph.facebook.com/'.$user['id'].'/picture?width=150\');"></div>';
							
							echo '<a href="?location=professor&pid='.$output['facebook_id'].'">'.$user['name'].'<br />';
							echo '<img src="http://graph.facebook.com/'.$output['facebook_id'].'/picture?height=100" /></a>';
							
							$notFoundP = false;
						
							echo '</div>';
						?></td><?php
						$counter = $counter + 1;
					}
				}//End of original code
				
				// Prints if 0 professors were found
				if ($notFoundP){
					?><td><?php echo 'Your search "'.$_REQUEST['search'].'"  returned 0 professors.'; ?></td><?php
				}
				?></tr></table><?php // End of professors table
				
				echo "<b>Schools: </b>";
				?> <table width = "75%" style="margin-top:00px;margin-left:0px;padding:0px;" ><?php
				// Prints out a list of schools with links to their page.
				
				/* ~~~~~~~~~~ Schools ~~~~~~~~~~ */
				?>
				<tr>
					<td align="center" bgcolor="#C0C0C0" width="25%" class="style">University</td>
					<td align="center" bgcolor="#C0C0C0" width="25%" class="style">City</td>
					<td align="center" bgcolor="#C0C0C0" width="25%" class="style">State</td>
				</tr> <?php
				while ($output = mysql_fetch_array($schoolID)){

					
						$fbid = $output['facebook_id'];
						$schools = $fb->api($fbid);
						// compares name from facebook id to searched string
						if (stristr($schools['name'],$searched)){
							echo '<tr>';
							echo '<td bgcolor="#ffffff" class="style"><a href="?location=university&universityid='.$output['facebook_id'].'">'.$schools['name'].'</a></td>';
							echo '<td bgcolor="#ffffff" class="style">'.$schools['location']['city'].'</td>';
				            echo '<td bgcolor="#ffffff" class="style">'.$schools['location']['state'].'</td>';
							echo '</tr>';
							$notFoundS = false;
						}
						
				}//End of original School loop

			// Print out that 0 results were found if nothing was returnd.
			if ($notFoundS){
				echo '<td>Your search "'.$_REQUEST['search'].'"  returned 0 schools.</td>';
			}
		?></table>
		<?php // End of Schools table
	}
		
		?><br /><br /><br />
</div>
