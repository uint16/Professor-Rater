
<?php

/*
* List of schools
* Author: Weixin Wu
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/

?>
<div style="margin-left:20px;" align="center">
<p><strong>SCHOOLS LIST</strong></p>
<br />
<p align="block" style="margin-left:20%; margin-right:20%;"> Having trouble spelling a schools name? You've come to the right place! You're one click away from
 finding your school! All you have to do is click on a letter below (Ie. "F" for Florida Institue of Technology or "U" for University of Florida)
and all schools in our database will be listed.</p> 

<br /><br />

<?php

//output "ALL" as a link

$x="ALL";
echo '<a href="?location=listofschools&schoolfirstLetter='.$x.'"> '.$x.'</a>'."<br>";

// Output all alphabet letters as a link

for ($i=65;$i<91;$i++){

	//Create a link with the letter
	
	echo '<a href="?location=listofschools&schoolfirstLetter='.chr($i).'"> '.chr($i).'</a>';
}
if (isset($_REQUEST['schoolfirstLetter'])){
	?>

	<table width="75%" border="0" cellspacing="1" cellpadding="0" style="margin-top:20px" bgcolor="#E6ECED" align="center">
	 <tr>
		<td align="center" bgcolor="#C0C0C0" height="30" width="25%" class="style">University Page</td>
		<td align="center" bgcolor="#C0C0C0" height="20" width="25%" class="style">City</td>
		<td align="center" bgcolor="#C0C0C0" width="25%" class="style">State</td>
	</tr>
	<tr></tr>
	<?php
	 
	//get schools' information through mysql

	$sql="SELECT * FROM schools";
	$query=mysql_query($sql);
	while($row=mysql_fetch_array($query)){

		/**
		 * when the firstletters of schools' names are same with the alphalts we click
		 * output the information, when we click "ALL", output all the information
		 */

		if(getstart($row['name'])==$_REQUEST['schoolfirstLetter']||$_REQUEST['schoolfirstLetter']=="ALL"){
			$school_fb=$fb->api($row['facebook_id']);
			?>

			<tr>
				<td bgcolor="#ffffff" class="style"><?php echo '<a href="?location=university&universityid='.$row['facebook_id'].'">'.$row['name'].'</a>'?></td>
				<td bgcolor="#ffffff" class="style"><?php echo $school_fb['location']['city']?></td>
				<td bgcolor="#ffffff" class="style"><?php echo $school_fb['location']['state']?></td>
			</tr>
			
		<?php
		}
	}
}
/**
 * the function of getting first letter of schools' and professors' name
 */
 
  function getstart($str){
	$asc=@ord(@substr($str,0,1));
	if($asc<160){
		if($asc>=48 && $asc<=57){
			return "1";
		}elseif($asc>=65 && $asc<=90){
			return chr($asc);
		}elseif($asc>=97 && $asc<=122){
			return chr($asc-32);
		}else{
			return "0";
		}
	}else{
		$asc=$asc*1000+@ord(@substr($str,1,1));
		if($asc>=176161 && $asc<176197){
			return "A";
		}elseif($asc>=176197 && $asc<178193){
			return "B";
		}elseif($asc>=178193 && $asc<180238){
			return "C";
		}elseif($asc>=180238 && $asc<182234){
			return "D";
		}elseif($asc>=182234 && $asc<183162){
			return "E";
		}elseif($asc>=183162 && $asc<184193){
			return "F";
		}elseif($asc>=184193 && $asc<185254){
			return "G";
		}elseif($asc>=185254 && $asc<187247){
			return "H";
		}elseif($asc>=187247 && $asc<191166){
			return "J";
		}elseif($asc>=191166 && $asc<192172){
			return "K";
		}elseif($asc>=192172 && $asc<194232){
			return "L";
		}elseif($asc>=194232 && $asc<196195){
			return "M";
		}elseif($asc>=196195 && $asc<197182){
			return "N";
		}elseif($asc>=197182 && $asc<197190){
			return "O";
		}elseif($asc>=197190 && $asc<198218){
			return "P";
		}elseif($asc>=198218 && $asc<200187){
			return "Q";
		}elseif($asc>=200187 && $asc<200246){
			return "R";
		}elseif($asc>=200246 && $asc<203250){
			return "S";
		}elseif($asc>=203250 && $asc<205218){
			return "T";
		}elseif($asc>=205218 && $asc<206244){
			return "W";
		}elseif($asc>=206244 && $asc<209185){
			return "X";
		}elseif($asc>=209185 && $asc<212209){
			return "Y";
		}elseif($asc>=212209){
			return "Z";
		}else{
			return "0";
		}
	}
}
?>
<br /><br />
</div>