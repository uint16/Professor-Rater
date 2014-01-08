<?php

/*
* University page and list of all professors teaching in this university
* Author: Damas Mlabwa
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/


?>


<style>
p{

}

box{
text-align:center;
width:auto;
height:auto;
border:2px solid gray;
margin:0px
}

#Container {
	width:760px;
	height:auto;
	margin:0px auto;
	text-align:left;
	padding:15px;
	padding-top:20px;
	background-color:#f4f4f4;
	border-width:1px;
	border-style:solid;
	}

</style>


<h1>
<title> University Page </title>
<link rel="stylesheet" type="text/css" href="style/university.css" />
</h1>

<div id = "Container">

<?php

//Get university id
$universityID = $_REQUEST['universityid'];

$university=$fb->api($universityID);
//print university page profile picture and name
echo sprintf('<img src="http://graph.facebook.com/%s/picture?type=large" width="150px" />', $university['id']);
echo '<h2>'.$university['name'].'</h2>';

//print "about" from university's facebook page
echo '<p align="left">'.$university['about'].'</p>';

if(!empty($university['description'])){
echo '<p align="left">'.$university['description'].'</p>';
}
?>
<div class="details">
<?php
//Print the number of people who likes this university's page
echo '<p align="left">'. $university['likes'].' People like ' .$university['name'].'</p>';

?>
</div>
</div>



<!-- Professor's at this University -->


<!-- <div class="professors-list"> -->

<div style="margin-left:20px;">
<h4 style="width:100%;">Professor's at this university</h4>
	

<?php
$school = sprintf("SELECT * FROM professors WHERE school='%s'", $university['name']);
$result = mysql_query($school);
$counter = 0;

echo '<table><tr>';
while($professor = mysql_fetch_array($result)){

	if ($counter == 5){
	echo '</tr>';
	echo '<tr>';
	$counter = 0;
	}
	
	$fbProfessor=$fb->api($professor['facebook_id']);
	//print each professor's facebook profile picture, name and make the image link to professor's page
	
	
	echo '<td width = 30% align="left" valign="top">';
	echo sprintf('<a class="frame" href="?location=professor&pid=%s">', $fbProfessor['id']);
	echo sprintf('<img src="http://graph.facebook.com/%s/picture?type=large" width="150px" />', $fbProfessor['id']).'<br/>';
	echo $fbProfessor['name'].'<br/>';
	echo "</a></td>";
	$counter++;
	
	
}
echo '</tr></table>';
?>
</div>
<!-- </div>-->
<!-- </div> -->
