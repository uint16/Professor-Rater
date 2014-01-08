<?php

/*
* Wrapping for all other pages. Header
* Author: Scotty Ward, Alexander Troshchenko
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/





?>


<html>
	<head>
		<title> Professor Rater! </title>
		<!-- Set up the page, stylesheets, scripts and etc. -->
		<meta charset="UTF-8" />
		<link rel="stylesheet" type="text/css" href="style/main.css" />
		<link rel="stylesgeet" type="text/css" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.8.2.min.js" ></script>
		<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
			
	
		<!-- Load SDK -->
		<?php

		// Load SDKs, connect to database.
		include "db_config.php";

		
		// Check location and set up a default one
		if (empty($_REQUEST['location'])){
		
		$_REQUEST['location'] = 'home';
		
		}
		
		?>
		
		
		
		
		
	</head>
	
	<body>
	
		<?php
		
		
			// Try to get Facebook user object. if he is not logged in, prompt logging in link and stop executing the page
			$user=$fb->getUser();
			
			if (!$user){
			
				echo '
					<p align="center">
					<a href="#" onclick="FB.login(function(response){window.location.reload();}, {scope: \'user_education_history,\'})"> Authorize Application with Facebook </a>
					</p>
				';
			
				exit();
				
				
			}
			// EVERYTHING AFTER THAT EXECUTED IF USER IS LOGGED IN, OTHERWISE APPLICATION WOULD EXIT
			
			
			
			// ----- Try to get User from the database -----
			
			// Get user facebook object and try to find his id inside database
			$user=$fb->api('/me');
			$sql=sprintf("SELECT * FROM users WHERE facebook_id='%s'", $user['id']);
			$result=mysql_query($sql);
			
			echo mysql_error();
			
			
			// Check whether there is anything in result or not
			if ($user_db=mysql_fetch_array($result)){
			
			
				//Generate profile Link if guests, go to user page, if no, go to professor.
				if($user_db['group']=='guests'){

					$profileLink=sprintf('index.php?location=user&uid=%s', $user['id']);
					
				} else if ($user_db['group']=='professors'){

					$profileLink=sprintf('index.php?location=professor&pid=%s', $user['id']);
				
				
				// Assume that if user does not belong to any listed group, he is banned
				} else {
				
				?>
				
				<p align="center"> You were banned from this application. If you believe that it was mistake or unfair ban, contact the administration.</p>
				
				
		
				
				<?php
					exit();
					return;
				}
				
				
				
				
				
			



		?>
			<!-- Load Menu -->
			<table class="menu">
				<tr>
					<td class="menu-button"><a href="index.php">HOME</a></td>
					<td class="menu-button"><a href="<?php echo $profileLink;?>">PROFILE</a></td>
					<td class="menu-button"><a onclick="window.location='index.php?location=listofschools'" href="#">SCHOOLS</a></td>
					<td class="logo"><img class="logo" alt="Professor Rater" src="logoEdited.gif" height="40px"> </td>
					<td class="menu-button"><a onclick="window.location='index.php?location=listofprofessors'" href="#">PROFESSORS</a></td>
					<td class="menu-button"><a onclick="window.location='index.php?location=bestprofessor'" href="#">CLASS SEARCH</a></td>
					<td class="menu-button"><input type="text" id="searchField" placeholder="Search schools/professors" /></td>
					<td class="menu-button"><a onclick="window.location='index.php?location=search&search='+$('#searchField').val();" href="#"><img class="logo" align="center" src="magnify-glass2.gif"></a></td>
				</tr>
			</table>
			
			
			
			
			
		<?php	
		
			// Not allow user to go to restricted locations
			$restricted_locations=array('registration', 'registration_ajax', 'professor_ajax', 'user_ajax', 'mysql', 'db_config', 'facebook_login');
		
		
			// If user attempted to go to any restricted location, swtich to homepage
			foreach ($restricted_locations as $location){
			
				if ($location==$_REQUEST['location']){
				
					$_REQUEST['location']="home";
				
				
				}
			
			
			}
		
			
			include $_REQUEST['location'].".php";
			
			$possible_admin=$fb->api('/me');
			// Try to find this user in administrators
			$sql=sprintf("SELECT * FROM admins WHERE facebook_id='%s'", $possible_admin['id']);
			$result=mysql_query($sql);
			
			// If there is user in admins list, show a link to control panel
			if (mysql_fetch_array($result)){
			
				echo "<p align=\"center\"><a href=\"?location=admin\"> Go to administration panel </a></p>";
			
			}
		
			//  If the user was not found. Load Registration prompt or page
			} else {
			
				
				// If user is trying to register, then lead him to registration page. Otherwise, prompt a message
				if ($_REQUEST['location']=='registration'){
				
					include "registration.php";
				
				
				} else {
			
			?>
					<div style="text-align:center">
			
					<!-- Prompt a message for registration -->
					<p> <b>Welcome!</b> </p>
					<br/>
					<p> We couldn't find you in our database. This can mean only two things: </p>
					<p> 1. This is the first time you're using our App, then you can register as a professor <a style="font-weight:bold" href="?location=registration&group=professors">here</a>, or as a student <a style="font-weight:bold" href="?location=registration&group=guests">here</a>.
					<p> 2. You deleted your page. If you want to return, please follow the links above to make a new account. </p>
					<br/>
					<p> We always appreciate our users and hope you will find what you are looking for, Thank You! </p>
					
					
					</div>

			<?php
				
				}
			
			
			
			
			}
			
			
			
		
		
		
		?>
	
	
	
	
	</body>


</html>