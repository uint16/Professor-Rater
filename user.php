<?php

/*
* User page with comment editing
* Author: Jeff Miller (Dropped course) finished by Alexander Troshchenko
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/





if (isset($_REQUEST['uid'])){
	//$user is the facebook id of the persons page being viewed
	$user=$fb->api($_REQUEST['uid']);
	
	//Find the list of professors that you have rated
	$row=mysql_query("SELECT * FROM users WHERE facebook_id='".$user['id']."'");
	$requestedUser=mysql_fetch_array($row);
	$ratedIDs=explode("," ,$requestedUser['rated_id']);
	
	//Get comments you have left
	$rowComments=mysql_query("SELECT * FROM comments WHERE author_id='".$user['id']."'");
	
}
?>

<title>Profile Page</title>

<link rel="stylesheet" type="text/css" href="style/userStylesheet.css" />

<div id="container">

<!--
+---------------------------------------------------+
| The StudentInfo section will display the users:   |
| photo, name, school, year of graduation and a     |
| link to their facebook profile.                   |
+---------------------------------------------------+
-->
<div id = "StudentInfo">
	<!-- 
	+----------------------------------------------------+
	| Set the users profile picture as the background to |
	| the profile image class. This allows the photo to  |
	| be centered both vertically and horizontally.      |
	+----------------------------------------------------+
	-->
	<style>
		.profileImage{
			background-image:url("http://graph.facebook.com/<?php echo $user['id']; ?>/picture?width=175")
		}
	</style>
	<div class = "profileImage">
	</div>
	<!--Put the users name in bold to help it stand out-->
	<h1><?php echo $user['name']; ?></h1>
	<!--
	+--------------------------------------------------+
	| This details section contains the users: school, |
	| year of graduation and profile link. It is       |
	| displayed in smaller                             |
	+--------------------------------------------------+
	-->
	<div class = "details">
	<?php
		//Locate the college inside of the users list of education
		foreach($user['education'] as $school){
			if ($school['type'] == "College"){
				$schoolName = $school['school']['name'];
				//See if your school is registered
				$rowSchool=mysql_query("SELECT * FROM schools WHERE name='".$schoolName."'");
				//If so fetch it's information
				$storedSchoolsName=mysql_fetch_array($rowSchool);
				//If your school name matches a school in the database print your school name as a link to the schools page
				if ($schoolName == $storedSchoolsName['name']){
					echo '<a href="'.$storedSchoolsName['facebook_id'].'">'.$schoolName.'</a><br />';
				}
				//Otherwise just print the name of your school
				else{	
					echo $school['school']['name']."<br />";
				}
				echo "Class of ".$school['year']['name']."<br />";
				echo '<a href="https://www.facebook.com/'.$user['id'].'">Facebook Profile</a>';
			}
		}
	?>
	</div>
</div>	
<?php
//Store the logged in users facebook id as $loggedUser
$loggedUser=$fb->api('/me');
//If the user is viewing their own page allow them to see comments etc. 
if ($loggedUser['id'] == $user['id']) { ?>
<!-- Professors are listed that you have rated -->
<div id = "RatingsSection">
	My Ratings<hr>
	<?php
	// Corrected by Alexander Troshchchenko
	
	// Get Liked and Disliked ID's from requestedUser
	$liked=explode(",",$requestedUser['liked_id']);
	$disliked=explode( "," , $requestedUser['disliked_id']);
	
	// Print out all users that were liked
	echo '<strong> Liked </strong><br/>';
	
	
	if (!empty($requestedUser['liked_id'])){
		//Run thorugh array of liked IDs
		foreach($liked as $constructed){
		
			$target=explode(":", $constructed);
			
			$target_fb=$fb->api($target[0]);
			
			echo sprintf("<p>  <b> %s </b> for class <b> %s </b></p>", $target_fb['name'], $target[1]);
			
		
		}
	
	// Output message that user hasn't any classes that he liked 
	} else {
	
		echo '<p> There are no positive ratings from you</p>';
	
	}
	
	
	
	
	
	echo '<strong> Disliked </strong><br/>';
	
	if (!empty($requestedUser['disliked_id'])){
	
		//Run through array of diliked IDs
		foreach($disliked as $constructed){
		
			$target=explode(":", $constructed);
			
			$target_fb=$fb->api($target[0]);
			
			echo sprintf("<p>  <b> %s </b> for class <b> %s </b></p>", $target_fb['name'], $target[1]);
			
		
		}
	
	
	} else {
	
		echo '<p> There are no negative ratings from you</p>';
	}
	
	
	?>
</div>
<div id = "CommentsSection">
	My Comments<hr>
	<?php
		while($requestedComments=mysql_fetch_array($rowComments)){
			echo '<div comment_id="'.$requestedComments['id'].'">'.str_replace("\\", "", $requestedComments['body']).'</div>';
			//echo '<a editComment = "1" href = "#" id = "'.$requestedComments['id'].'">  Edit</a><br /><br />';
			echo '<a editComment="1" href="#" comment_id="'.$requestedComments['id'].'"> Edit</a>';
			echo '<a editComment="2" style="display:none" href="#" comment_id="'.$requestedComments['id'].'"> Submit </a>';
			echo '<br /><br />';
		}
	?>
</div>
<?php }?>

<?php 
// Add delte your page button
if ($loggedUser['id'] == $user['id']) { ?>
<a href="#" onclick="deletePage()">Delete your page</a>
<?php } ?>



<script type="text/javascript">


	//Use professor's delete function to delete your page
	function deletePage(){
	
	// Prompt confirmation
		if (confirm('Are you sure you want to delete your page? Note: This action cannot be undone')){
			
			$.ajax({
			
			type: "POST",
			url: "professor_ajax.php",
			data: {id: '<?php echo $loggedUser['id'];?>', action: "delete_page"}
			
			
			}).done(function (response){
			
				// If the page was successfully deleted, prompt the dialog and reload the page
				if (response==""){
				
					alert("Your page was successfully deleted, please hold still and you'll be redirected to the home page");
					window.location="index.php";
				
				// If user got another response, then something happened and ask him to contact the support.
				} else {
				
					alert(response+". Please contact support with this error.");
				
				}
			
			
			
			});
			
		
		}
	
	
	
	
	}

	// Handle clicking "edit word" button
	$('a[editComment="1"]').click(function(){
		//Get id of the word
		var id=$(this).attr("comment_id");
		//Get body text
		var text=$('div[comment_id="'+id+'"]').html();
		//Replace stuff with the textfield
		$('div[comment_id="'+id+'"]').html('<textarea comment_id="'+id+'" cols="37" rows="4">'+text+'</textarea>');
		//Show new button
		$('a[editComment="2"][comment_id="'+id+'"]').show(100);
		//Hide old one
		$('a[editComment="1"][comment_id="'+id+'"]').hide(100);
	});

	//Handle clicking Save word
	$('a[editComment="2"]').click(function(){
		//Get id of the word
		var id=$(this).attr("comment_id");
		//Get body text
		var text=$('textarea[comment_id="'+id+'"]').val();
		$.ajax({
		url: 'user_ajax.php',
		type: 'POST',
		data: {method: "samplePost", text: text, id: id}
	}).done(function(response){
		// Replace textarea with simple text
		$('div[comment_id="'+id+'"]').html(response);
		});
		//Show new button
		$('a[editComment="1"][comment_id="'+id+'"]').show(100);
		//Hide old one
		$('a[editComment="2"][comment_id="'+id+'"]').hide(100);
	});


</script>


</div>