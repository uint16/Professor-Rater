<?php
/*
* Dummy Administration control panel.
* Author: Alexander Troshchenko
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/


/*

1. Display Reports.

TODO

2. Implement banning system

3. Implement comment and user  reports actions

4. MySQL console

5. Adding and removing administrators


*/

// Check whether user is a valid admin

	// Get user facebook object rom wrapping and re store it for local use.
$user_fb=$user;

	// Try to get user form the admins list.
	$result=mysql_query(sprintf("SELECT * FROM admins WHERE facebook_id='%s'", $user_fb['id'] ));
	
	if (mysql_fetch_array($result)){
	
		// Use three different styles for three different types of comments blocks
		$style_processing="color:#4A4A4A;border-color:#FF9500;border-width:1px;border-style:solid;padding:5px;margin:10px;"; // Non-checked Comments 
		$style_approved="color:#4A4A4A;border-color:#3BC429;border-width:1px;border-style:solid;padding:5px;margin:10px;"; // Approved Comments
		$style_rejected="color:#4A4A4A;border-color:#E33232;border-width:1px;border-style:solid;padding:5px;margin:10px;"; // Rejected;
		
	// Do Dummy add-commenting.
	?>
	
		
		<!-- Table containing reports -->
		<strong> Review Reports: </strong>
		<table>
		
		
			<!-- Titles -->
			<tr>
				<td> Comments Reports </td>
				<td> Users Reports </td>
				
			</tr>
			
			
			<!-- Actual Reports -->
			<tr>
			
				<!-- Comments Reports -->
				<td style="width:50%">
				
				<?php
				// Select all comments reports
				
				$result=mysql_query("SELECT * FROM comments_reports ORDER BY status_id");
				echo mysql_error();
				
				// Loop through all comments
				while ($report=mysql_fetch_array($result)){
					
					// Open a div
					echo "<div style=\"";

					// Add a style
					$status=$report['status_id'];
					
					if ($status=='1'){
					
						echo $style_processing;
					
					} else if ($status=='2'){
						
						echo $style_approved;
					
					} else {
					
						echo $style_rejected;
					
					}
					
					// Close opening tag
					echo '" >';
				
				
					// Get the comment that is reported
					$sql= sprintf("SELECT * FROM comments WHERE id='%s'", $report['comment_id']);
					$subresult=mysql_query($sql);
					echo mysql_error();
					
					if ($comment=mysql_fetch_array($subresult)){
					
						echo $comment['body'];
						
						echo '<br/> Author id:'.$comment['author_id']; 
						
					} else {
					
					
						echo "<i>The comment was deleted by user</i>";
					}
					
					// Close the tag
					echo "</div>";
				
				
				
				}
				
				
				
				
				?>
				
				
				</td>
			
			
				<!-- Users Reports -->
				<td>
				
				
						<?php
				// Select all comments reports
				
				$result=mysql_query("SELECT * FROM professors_reports ORDER BY status_id");
				echo mysql_error();
				
				// Loop through all comments
				while ($report=mysql_fetch_array($result)){
					
					// Open a div
					echo "<strong style=\"font-size:10px;\"> Reported User ID: </strong>".$report['reported_id']."<br/>";
					echo "<div style=\"";

					// Add a style
					$status=$report['status_id'];
					
					if ($status=='1'){
					
						echo $style_processing;
					
					} else if ($status=='2'){
						
						echo $style_approved;
					
					} else {
					
						echo $style_rejected;
					
					}
					
					
					// Close opening tag
					echo '" >';
				
				
				
					
						echo $report['reason'];
						
					
					
					// Close the tag
					echo "</div>";
				
				
				
				}
				
				
				
				
				?>
				
				
				
				
				
				</td>
			</tr>
			
			
		</table>
		<br/>
		<br/>
		<br/>
		<!-- Display settings -->
		<strong style="margin-left:100px;margin-top:20px;"> Change Applicaiton Settings </strong>
		<table style="margin-left:100px;">
		
			<?php
			// Load all settings from database
			$result=mysql_query("SELECT * FROM options");
			
			// Run through all options
			while ($option=mysql_fetch_array($result)){
				?>
				
				<tr>
				
					<td>
					
						<?php echo $option['description']; ?>
					
						
					</td>
					
					<td>
					
						<input type="text" id="option_<?php echo $option['id']; ?>" value="<?php echo $option['value']; ?>" />
					
					</td>
					
					<td>
					
						<input type="submit" onclick="saveOption(<?php echo $option['id']; ?>)" value="Save" />
					
					</td>
					
				
				</tr>
				
				<?php
						
			}
			
			
			
			?>
		
		
		</table>
	
		
		<!-- Different Scripts for admin AJAX -->
		<script>
		
		// Save options
		function saveOption(oid){
			
			
			// Simply form ajax request
			$.ajax({
				
				type: "POST",
				url: "admin_ajax.php",
				data: {
				
					action: "save_option",
					id: oid,
					value: $("#option_"+oid).val()
				}
				
				
			}).done(function(response){
			
				if (response==""){
				
					alert("Option saved sucessfully");
				
				}else {
				
					alert(response);
				
				}
			
			
			});
			
		}
		
		</script>
		
		
	
	
	
	
	<?php
	} else {
	
	echo '<p align="center" style="border-color:#FF0000;border-style:solid;padding:5px;margin:5px;border-width:1px;"><strong style="color:#FF0000"> Sorry, but you\'re not an administrator </strong></p>';
	
	}





?>