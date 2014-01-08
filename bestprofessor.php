<?php
/*
* Best professor for specific class within the university.
* Author: Damas Mlabwa
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/

?>


<h1>
<title> Find Best Professor for your class!</title>
</h1>

<strong> 1. Select university you're planning to take course in. (Note, you can select field and start typing university name and it will appear) </strong><br/>
<SELECT style="margin:15px;" id="schools_list" >

	<?php
		$school = "SELECT * FROM schools";
		$result = mysql_query($school);

		while($currentschool = mysql_fetch_array($result)){
			echo '<OPTION VALUE ="'.$currentschool['facebook_id'].'">'.$currentschool['name'].'</option>';
				}
	?>

</SELECT><br/><br/>



<strong>2. Enter Course Number, no spaces. All capitilized (CSE2010 for example) </strong><br/><br/>
<input style="margin:15px;" type="text" id="search-course" /><a class="button" onclick="searchForClass()"> Search Best Professor for This Class</a><br/><br/>
	


<?php
include 'mysql.php';

//get user input and assign to variables
if (isset($_REQUEST['schoolID'])&&isset($_REQUEST['subject'])){

echo "<strong>3. Now you have all professors listed matching your criteria </strong><br/>";
	$subject = $_REQUEST['subject'];
	$school = $_REQUEST['schoolID'];
	
	
	//perform operations only if user has typed a term to search for and has selected a university, else show message
	if(!empty($subject) && !empty($school)){
		$university=$fb->api($school);
	
		//check the length of search term, a valid search term must be at least 3 characters long
		//perform operations only if input is valid, else show message 
		if(strlen($subject)>=3){

	
			//get results containing search term from database
			$query = "SELECT * FROM professors WHERE subjects LIKE '%".mysql_real_escape_string($subject)."%' AND school ='".$university['name']."' ORDER BY ratio DESC";
			$query_run = mysql_query($query);
			$query_num_rows = mysql_num_rows($query_run);

			//perform operations only if there's a result to show, else show message
			if($query_num_rows >= 1){
				echo $query_num_rows.' Results found:<br>';
				echo '<br>';
				echo 'Click on professor to view profile, and all comments'.'<br/>';
				echo '<table><tr>';
				$counter = 0;
				while($query_row = mysql_fetch_array($query_run)){
					if ($counter == 3){
						echo '</tr>';
						echo '<tr>';
						$counter = 0;
					}
					
					$fbProf=$fb->api($query_row['facebook_id']);

					//display some info about professor, just trial not everything will be shown in final page
					echo '<td width = 30% align="left" valign="top">';
					echo sprintf('<a class="frame" href="?location=professor&pid=%s">', $fbProf['id']);
					echo sprintf('<img src="http://graph.facebook.com/%s/picture?type=large" width="150px" />', $fbProf['id']).'<br/>';
					echo $fbProf['name'].'<br/>';
					echo 'Courses : '.$query_row['subjects'].'<br/>';
					echo 'Likes   : '.$query_row['likes'].'<br/>';
					echo 'Dislikes: '.$query_row['dislikes'].'<br/>';
					echo 'Ratings : '.$query_row['ratings'].'<br/>';
					echo 'Ratio   : '.round($query_row['ratio'], 1).'<br/>';
					echo "</a></td>";
					$counter++;
				}
			} else {
				echo '<td>No results found'.'<br/></td>';
			}
			echo '</tr></table>';
		} else {
			echo 'Input must be at least three(3) characters long.'.'<br>';
			echo 'Example search terms:'.'<br/>';
			echo 'Course prefix, example HUM, CSE, MTH'.'<br/>';
			echo 'Course number, example 1002, 2010, 1234'.'<br/>';
			echo 'Combination of both, example CSE2010, HUM1234, MTH1002'.'<br/>';
		}
	} else {
		
		echo 'Please check your input'.'<br/>';
		echo 'No input in search or no university is selected'.'<br/>';
	
	}
}
?>


<script>

function searchForClass(){

var searchCourse=$('#search-course').val();

// Get rid of all invalid characters

var schoolID=$("#schools_list>option:selected").val();

window.location="?location=bestprofessor&schoolID="+schoolID+"&subject="+searchCourse;

}

</script>
</div>
</html>