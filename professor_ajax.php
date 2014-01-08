<?php
/*
* PHP Script that handles php AJAX requests from professor.php page 
* Author: Alexander Troshchenko
* Class: CSE 2010
* 
* Part of Group 3 Facebook Project Professor Rater
*
*/



// Get Facebook Object and connect to database
include "facebook_login.php";
include "mysql.php";
	
// Abort code if no action was received	
if (!isset($_REQUEST['action'])){

	echo "Error #0000: No action Specified";
	exit();

}	



//Handle Different actions
	
	// Adding comment
	if ($_REQUEST['action']=='add_comment'){
		
		// Check whether required fields are filled in
		$required_fields=array( 'author', 'target', 'body', 'liked', 'subject');
		
		// Run through each field and exit if any field is empty
		foreach ($required_fields as $field){
		
			
			
			if (str_replace(" ", "",$_POST[$field])==""){
				echo "nullfield";
				exit();
			}
			
					
		
		}
	
		// Process string. Node, this part of code can be used on other pages
		$restricted_strings=array('<', '>');
		$replacements=array('[', ']');
		$processed_comment=str_replace($restricted_strings, $replacements,$_POST['body']);
	
		// Construct a MySQL Reques and send it
	
		$sql=sprintf("INSERT INTO comments VALUES (null, '%s', '%s', '%s', '%s', 0 )",$_POST['author'],$_POST['target'],$_POST['subject'], $processed_comment );
		mysql_query($sql);
	
	
		// Print errors
		if (mysql_error()){
	
			echo mysql_error();
	
		}
	
		
		// Alter professor rating
		
			// Form request and get professor's ratings
		$sql=sprintf("SELECT likes, dislikes, ratings FROM professors WHERE facebook_id='%s'",$_POST['target']);
		$professor_rating=mysql_fetch_array(mysql_query($sql));
		
		if (mysql_error()){
		
			echo mysql_error();
		
		}
		
		
			//  Assign variables
		$likes=(int)$professor_rating['likes'];
		$dislikes=(int)$professor_rating['dislikes'];
		$ratings=(int)$professor_rating['ratings'];
		
		
		// Increment likes or dislikes depending on what user entered
		if ($_REQUEST['liked']=='1'){
			
			$likes=$likes+1;
		
		} else {
			
			$dislikes=$dislikes+1;
		}
		
		$ratio=$likes/$dislikes;
		$ratings=$ratings+1;
		
		// Update data on professor's page
		
		$sql=sprintf("UPDATE professors SET likes='%d', dislikes='%d', ratings='%d', ratio='%f' WHERE facebook_id='%s' ", $likes, $dislikes, $ratings, $ratio,$_POST['target']);
		mysql_query($sql);
		
		if (mysql_error()){
		
			echo mysql_error();
			
		}
		 

		// Get settings from database.
		$record_rated=mysql_fetch_array(mysql_query("SELECT value FROM options WHERE name='record_rated' "));
		$enable_notifications=mysql_fetch_array(mysql_query("SELECT value FROM options WHERE name='enable_notifications'"));
		
		
		// If settings set to record who rated what class
		if ($record_rated['value']=='1'){
		
			// Construct a rated_id:class and append it to already rated classes
			$rated=$_REQUEST['target'].':'.$_REQUEST['subject'];
			
			$current_info=mysql_fetch_array(mysql_query(sprintf("SELECT rated_id, liked_id, disliked_id FROM users WHERE facebook_id='%s'",$_POST['author'])));
			
			
			// Choose to add to liked ids or disliked and construct SQL request
			if ($_REQUEST['liked']=='1'){
			
				//Construct liked id
				if ($current_info['liked_id']==""){
				
					$constructedLikedId="";
				
				} else {
				
					$constructedLikedId=$current_info['liked_id'].",";
				}
			
				
				$sql=sprintf("UPDATE users SET liked_id = '%s' , rated_id= '%s'  WHERE facebook_id='%s'", $constructedLikedId.$rated ,  $current_info['rated_id'].",".$rated,$_POST['author'] );
			
			
		
			} else {
			
				// Construct disliked ID
				if ($current_info['disliked_id']==""){
				
					$constructedDislikedId="";
				
				} else {
				
					$constructedDislikedId=$current_info['disliked_id'].",";
				}
			
			
				$sql=sprintf("UPDATE users SET disliked_id = '%s' , rated_id= '%s'  WHERE facebook_id='%s'", $constructedDislikedId.$rated ,  $current_info['rated_id'].",".$rated,$_POST['author'] );
			
			}
			
			
			
			
			mysql_query($sql);
			if (mysql_error()){
			
				echo mysql_error();
				
			}
		
			
			
			
		
		}
		
		
		// Notify person if he or she was rated
		if ($enable_notifications['value']=='1'){
			
			// Link to redirect on clicking notification
			$link="?location=professor&pid=".$_REQUEST['target'];
			
			// Text to say depending on like or dislike
			if ($_REQUEST['liked']=='1'){
			
				
				$text="Someone left a positive comment on your page and your new ratio is ".round($ratio, 2).", go check it out!";
		
			} else {
			
				$text="Unfortuanately, someone disliked you. Your new ratio is ".round($ratio, 2);
			}
			
			
			// Actually sen the notification
			$response=$fb->api('/'.$_REQUEST['target'].'/notifications', 'POST', array(
				
				'access_token' => $fb->getAppId() . '|' . $fb->getApiSecret(),
				'href' => $link,
				'template' => $text
				));
		
		
		}
		
		exit();
	}

	
	// Delete users page
	if ($_REQUEST['action']=='delete_page'){
	
	if (!isset($_REQUEST['id'])){
	
	echo "You have not specified ID to delete, if you're not an administrator, please report to support service";
	
	exit();
	}
	
	
		// Delete all the comments related to that person
		
		$sql=sprintf("DELETE FROM comments WHERE professor_id='%s'",$_POST['id'] );
		mysql_query($sql);
		
		
		// Output any errors that could occur at deleting comments
		if (mysql_error()){
		
			echo mysql_error();
		
		}
		
		
		/*  Commented out: Do not remove authored comments. Person is still responsible for what he has wrote
		// Delete all the comments authored by the person
		$sql=sprintf("DELETE FROM comments WHERE author_id='%s'",$_POST['id'] );
		mysql_query($sql);
		
		
		// Output any errors that could occur at deleting comments
		if (mysql_error()){
		
			echo mysql_error();
		
		}
		*/ 
		
		// Delete user from users list
		$sql=sprintf("DELETE FROM users WHERE facebook_id='%s'",$_POST['id'] );
		mysql_query($sql);
		
		
		// Output any errors that could occur at deleting comments
		if (mysql_error()){
		
			echo mysql_error();
		
		}
		
		
		
		// Delete user from professors list and user's list
		$sql=sprintf("DELETE FROM professors WHERE facebook_id='%s'",$_POST['id'] );
		mysql_query($sql);
		
		
		// Output any errors that could occur at deleting comments
		if (mysql_error()){
		
			echo mysql_error();
		
		}
		
		exit();
		
		
	
	
	}
	
	
	// Update profile
	if ($_REQUEST['action']=='update_profile'){
	
	
		
	
		// Simply update user profile
		$sql=sprintf("UPDATE professors SET subjects='%s', school='%s' WHERE facebook_id='%s' ",$_POST['subjects'],$_POST['school_name'],$_POST['user_id']);
		mysql_query($sql);
		
		// Print out error if there is any
		if (mysql_error()){
		
			echo mysql_error();
		
		}
	
		exit();
	}


	// report comment handling
	if ($_REQUEST['action']=='report_comment'){
	
		// Check presence of the id
		if(empty($_REQUEST['id'])){
		
			echo "No comment ID specified, contact the support";
			exit();
		
		}
		
		
		// Update the report count of the comment, so it won't be showed 
		$sql=sprintf("UPDATE comments SET reports='1' WHERE id='%s' ", $_POST['id'] );
		mysql_query($sql);
		
		if (mysql_error()){
			echo mysql_error();
			return;
		}
		
		
		
		// Add report to the databse so admin could change it
		$sql=sprintf("INSERT INTO comments_reports VALUES(NULL, %s, 1)", $_POST['id']);
		mysql_query($sql);
		if (mysql_error()){
			echo mysql_error();
			return;
		}
		
		
		
		
	}
	
	
	
	// Report professor handling
	if ($_REQUEST['action']=='report_professor'){
	
		// Check presence of all variables
		$required_variables=explode(" ", "action id reason author");
			
		foreach ( $required_variables as $variable){
		
			
			if (empty($_REQUEST[$variable])){
			
				echo "There is problem with the AJAX script, please contact the support";
			
			}
		
		
		}
		
		
		

		
		// Process string. Node, this part of code can be used on other pages
		$restricted_strings=array('<', '>');
		$replacements=array('[', ']');
		$processed_reason=str_replace($restricted_strings, $replacements, $_POST['reason']);
		
		
		//Form a report in database with review status 1. 
		$sql=sprintf("INSERT INTO professors_reports VALUES (NULL, '%s', '%s', '%s', 1)",$_POST['id'], $_POST['author'], mysql_real_escape_string($processed_reason) );
		mysql_query($sql);
		
		if (mysql_error()){
		
		//echo mysql_error();
		echo "There is an error in database request, please contact the support";
		
		exit();
		}
		
	
	
	}
	




?>