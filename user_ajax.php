<?php
/*
* AJAX Script for university page
* Author: Jeff Miller
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/





	include "mysql.php";
	
	// Process Comment
	$restricted_strings=array('<', '>');
	$replacements=array('[', ']');
	$processedComment=str_replace($restricted_strings, $replacements, $_POST['text']);
	
	
	
	
	
	// Update database
	mysql_query("UPDATE comments SET body='".$processedComment."' WHERE id='".$_POST['id']."'");
	
	
	// Echo it
	
	echo str_replace("\\", "", $processedComment);
	echo mysql_error();
	return;
	//Look at update function



?>