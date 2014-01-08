<?php

/*
* AJAX Script to handle requests form registration page
* Author: Alexander Troshchenko
* CSE 2010 Facebook Project Group 3 
*/



include "mysql.php";



// If the script recieved the action
if (isset($_POST['action'])){



	if ($_POST['action']=='add_school'){

		include "facebook_login.php";
		
		// Handle empty school link
		if (!isset($_POST['link'])){

			echo "error";
			exit();		
		}
		
		
		
		
		// Process link
		
		
		// Assume that id of the 
		$facebook_id=substr($_POST['link'], strrpos( $_POST['link'],'/', -2) + 1 );
		
		
		// Try to get facebook object of a school
		
		try{
	
		$school_fb=$fb->api($facebook_id);
		
		
		// If error happens, such as graph exception or something like that, output as an input error
		} catch (Exception $e){
	
		echo "school_link_corrupt";
		
		exit();
		
		}
		
		// Add school to database
		$sql=sprintf("INSERT INTO schools VALUES(NULL, '%s' , '%s', '%s' )", $school_fb['name'], $school_fb['id'], $school_fb['link'] );
		mysql_query($sql);
		
			
		// Output as other error
		if (mysql_error()!=""){
		
		echo "error";
		
		exit();
		}
		
		
		echo $school_fb['name'];
		
		
		
		
		
		
	
		
		
		
		return;
	
	} 
	
	
	// Add professor to databas
	if ($_POST['action']=='add_professor'){
		
		// Check whether all variables are filled in
		$required_values=array('id', 'classes', 'school');
		
		
		// Run through each value and stop thefunction if at least one is missing
		foreach($required_values as $value){
		
			if ( !isset($_POST[$value])) {
				
				echo $value." is not filled in";
				
				return;
			} else {
			
				if ($_POST[$value]==""){
					
					echo $value." is not filled in";
				
					return;
				
				
				}
			
			
			}
		
		}
		
	
	
		// Construct SQL query
		
		
		// 											facebook_id	school	subjects	likes	reports	dislikes	ratings	ratio ratio
	
		$sql=sprintf("INSERT INTO professors VALUES ('%s', '%s', '%s', 1 , 0, 1 , 0 , 1 ) ", $_POST['id'], $_POST['school'], $_POST['classes']);
		mysql_query($sql);
		
		if (mysql_error()){
		
		echo mysql_error();
		
		} 
		
		// Add to users list
		
		// 											id	facebook_id	group	rated_id	liked_id	disliked_id
		$sql = sprintf(" INSERT INTO users VALUES (NULL, '%s', 'professors', '', '', '') ", $_POST['id']);
		mysql_query($sql);
		
		if (mysql_error()){
		
		echo mysql_error();
		
		} 
		
		return;
	
	}
	













	// Exit after executing script

	exit();
}



?>