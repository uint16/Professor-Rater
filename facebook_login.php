<?
/*
* Simple login to FB api and MySQL database
* Author: Alexander Troshchenko
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/

include "mysql.php";
//Facebook Api login and difinition
 
  $facebook_appID = '356047577806156';
  $facebook_secret = '6daf6bdf16b1d28f59cd3b0809b4aa40';
  
  
 require_once('facebookapi/facebook.php');
  
  //Create a config to Login
  $config=array();
  $config['appId']=$facebook_appID;
  $config['secret']=$facebook_secret;
  
  $fb= new Facebook($config);





?>