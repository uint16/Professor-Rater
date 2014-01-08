<?php
/*
* Dummy Administration control panel AJAX.
* Author: Alexander Troshchenko
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/

include "mysql.php";
// We don't expect error handling for admin AJAX. We expect administration to be familiar with code
if ($_POST['action']=="save_option"){

mysql_query("UPDATE options SET value='".$_POST['value']."' WHERE id='".$_POST['id']."'");

echo mysql_error();
}

?>