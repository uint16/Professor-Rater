<?php

/*
* Simple Registration Page
* Author: Alexander Troshchenko
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/


?>


<!-- Override Stylesheet -->

<style>

td {

border-style:solid;
padding:20px;
border-width:1px;
border-radius:2px;
}

table {

margin: 20px;


}


</style>



<?php


$user_fb=$user;



if (isset($_REQUEST['auto_register'])){



// Implement autoregisrtation and exit
$random_classes=array('CSE2010', 'HUM2010', 'TEST15', 'CHM1002', 'HUM1001', 'HUM1002', 'HUM1003', 'HUM1004');
shuffle($random_classes);

$random_schools=array('Florida Institute of Technology', 'University of Florida', 'University of Central Florida');
shuffle($random_schools);


// Each Auto-professor will have 3 classes and will be registered at random school
$sql=sprintf("INSERT INTO professors VALUES( '%s', '%s', '%s' , 1, 0 , 1 , 1 , 1) ", $user_fb['id'],  $random_schools[0], $random_classes[0].','.$random_classes[1].','.$random_classes[2] );
mysql_query($sql);


$sql=sprintf("INSERT INTO users VALUES(null, '%s', 'professors', '', '', '') ", $user_fb['id']);
mysql_query($sql);


echo '<p> You have successfully registered. Thank you for automated registration. </p>';
exit();
}








// Check whether the group of user is setted or not
if (isset($_REQUEST['group'])){


	// Register Professor's
	if ($_REQUEST['group']=='professors'){?>
	
		
		<table>
		
			<!-- Prompt -->
			<tr>
			
				<td>
				
					Thank you for Choosing <b>Professor Rater</b>. Please give us some information about you 
				
				</td>
			
			
			</tr>
		
			<!-- Customizing profile -->
			<tr valign="top">
				
				<!-- Add Classes -->
				<td>
				<strong> Add your classes </strong><br/>
				<span style="font-size:10px;color:#AEA7C4;"> Class codes (CSE2010, SBJ1002). NO spaces, 1 class code per field. Thank you. </span>
				<br/>
				<br/>
				
				<p id="initialRow"></p>
				
				<a id="addClass" href="#"> Add one more class </a>
				</td>
				
				
				
				<td>
				 <strong> Select your School: </strong><br/>
					<!-- Select school from existing ones -->
					<select id="select-school-selector">
					
						<?php
						
						// List all schools as <option> school name </option>
						$schools_nonfetched=mysql_query("SELECT * FROM schools");
						while ($school=mysql_fetch_array($schools_nonfetched)){
						
							echo sprintf('<option value="%s">%s</s>', $school['name'], $school['name'] );
			
						}
						
						?>
					
					</select><br/><br/>
					
					<tag id="add-school-entry">
					<strong>Didn't find your school?</strong><br/>

					
					Add one by simply inserting a link to the facebooks page! 
					
					<input type="text" id="add-school-input" placeholder="Enter Facebook Link here" /> <a href="#" onclick="addSchool(this)" > Add School </a>
					</tag>
					
					
				
				</td>
			
			</tr>
			
		</table>
		
		<p align="right"> <a class="button" href="#" onclick="finishRegistration()" > Finish Registration </a> </p>
	
	<!-- Scripts that will handle each event -->
	<script>
	
		var columnIndex=1; // Initial column indexing, actually counts classes added 
	
	
		// Add one more class entry
		$("#addClass").click(function (){
	
			// Construct elements of the row
			var newClassInput="<input type=\"text\" class=\"subject-element\" id=\"subject-element-"+columnIndex+"\" />";
	
			var newClassDeleteButton="<a href=\"#\" onclick=\"$(this).remove();$('#subject-element-"+columnIndex+"').remove()\"> Remove this Class <a>";
	
	
			// Construct the whole row
			var newClass="<p>"+newClassInput+newClassDeleteButton+"</p>";
	
	
	
			$("#initialRow").append(newClass);
	
			// Increment index
			columnIndex++;
	
		});
	
	
	
		// Function adds school and removes school field and button so User doesn't enter several schools
		function addSchool (button){
		
			//Get a link
			var link=$("#add-school-input").val();
			
			
			// Handle empty fields
			if (link.replace(" ", "")==""){
				
				alert ("School field is empty");
				
				return;
			
			}
			
			
			// Try ajax to add a school to database
			$.ajax({
			
			type: "POST",
			url: "registration_ajax.php",
			
			data: {
			action: "add_school",
			link: link
			
			}
			
			
			
			
			// Handle response
			}).done(function(response){
			
				
				
				// Handle wrong link
				if (response=="school_link_corrupt"){
					
					alert("School link is incorrect, try to remove anything that seems to be not part of school name. If error still exists, contact the support. Sorry for inconvenience.");
				
				// Handle non-error-responses
				}else if (response!="error"){
				
				
					// Add an option in a school list
					$("#select-school-selector").append("<option value=\""+response+"\">"+response+"</option>");
					
					// Delete add school entry
					$("#add-school-entry").remove();
				// Handle occured errors
				} else {
				
					alert("Error occured. Please Contact the Support! ");
					
					return;
				}
				
			});
			
			
			
		
		}
	
	
		
		// Submits registration and 
		function finishRegistration(){
		
			// Construct a list of classes
			var classes="";
			
			// For each existing field with class-code, add a class to the list
			$(".subject-element").each( function(){
			
				classes+=$(this).val()+",";
			
			});
			
			
			// Return an error if no classes are inputted
			if (classes.length==0){
			
				alert("You cannot have zero classes");
				return;
				
			}
			
			
			// Get rid of last comma
			classes=classes.substring(0, classes.length-1);
		
		
			
			// Send everything to AJAX and in case of success prompt user to write a post on the wall
			
			$.ajax({
			
				type: "POST",
				url: "registration_ajax.php",
				data:{
				
					action: "add_professor",
					id: "<?php echo $user_fb['id']?>",
					classes: classes,
					school: $('#select-school-selector>option:selected').val(),
				
				}
				
			// Handle response
			}).done( function (response){
			
				// Success
				if (response == ""){
				
					FB.ui(
					  {
						method: 'feed',
						name: 'Professor Rater',
						link: 'http://apps.facebook.com/professorrater',
						picture: 'http://professor-rater.com/facebook_app/logo.gif',
						caption: 'I\'ve registered on Professor Rater!',
						description: 'You can now rate me on Professor Rater or Register if you are a professor at any school'
					  },
					  function(response) {
						if (response && response.post_id) {
						  alert('Thank you for republishing, We will now redirect you to the homepage');
						
						} else {
						  alert('Thank you for registration! You will be redirected to the homepage');
						 
						}
						
						
						  window.location="index.php";
						
					  }
					);
				
				} else {
					
					alert (response);
					return;
			
				}
			
		
			});
			

		
		
		}
	
	</script>
	
	
	
	
	
	
	
	<?php
	
	return;
	} // End of Professor Registration
	
	
	// Register Casual User
	if ($_REQUEST['group']=='guests'){ 
	
	
	// Simply insert user into db
	$sql=sprintf("INSERT INTO users VALUES(null, '%s', 'guests', '', '', '') ", $user_fb['id']);
	mysql_query($sql);
	
	
	?>
	
	<div align="center" style="margin-left:300px;margin-top:100px;padding:8px;border-style:solid;border-width:1px;border-radius:2px;width:600px;"> We added you to our database, you are almost done! <a href="#" onclick="finish()"> Finish Registration </a> </div>
	
	<script>
	
 function finish(){
					FB.ui(
						  {
							method: 'feed',
							name: 'Professor Rater',
							link: 'http://apps.facebook.com/professorrater',
							picture: 'http://professor-rater.com/facebook_app/logo.gif',
							caption: 'I\'ve registered on Professor Rater!',
							description: 'I can now rate all professors so all other students will choose the right one.'
						  },
						  function(response) {
							if (response && response.post_id) {
							  alert('Thank you for republishing, We will now redirect you to the homepage');
							
							} else {
							  alert('Thank you for registration! You will be redirected to the homepage');
							 
							}
							
							
							  window.location="index.php";
							
						  }
						);

				  }
	</script>
<?php	}
	
	
} // End of Registration with specified Group

?>