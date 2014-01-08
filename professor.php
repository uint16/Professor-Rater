<?php
/*
* Professor's page.
* Author: Alexander Troshchenko
* Group 3 Facebook Project, CSE 2010, Fall 2012
*/

/*
 Get all important variables.
*/

// Get options

//Maximum reports for the comment
$max_comment_reports_notfetched=mysql_query("SELECT value FROM options WHERE name='max_comment_reports'");

$temp=mysql_fetch_array($max_comment_reports_notfetched);

$maximum_reports_for_comment=$temp['value']; // Maximum reorts possible for comment, if reports are more or equals than that valuem then comment is not showed





// Get professor info object
$professor_fb=$fb->api($_REQUEST['pid']);

//Get professor from Database
$sql=sprintf("SELECT * FROM professors WHERE facebook_id='%s '", $professor_fb['id']);
$result=mysql_query($sql);

$professor_db=mysql_fetch_assoc($result);

// Get Classes that professor teach
$classes=explode(',', $professor_db['subjects']);

//Get user from Facebook and user from database
$user_fb=$user;
$sql=sprintf("SELECT * FROM users WHERE facebook_id='%s '", $user['id']);
$result=mysql_query($sql);

$user_db=mysql_fetch_assoc($result);


//Get school of the professor
$sql=sprintf("SELECT * FROM schools WHERE name='%s '", $professor_db['school']);
$result=mysql_query($sql);
  
$school_db=mysql_fetch_assoc($result);
//$user=$fb->api('/me/'); is included.
/* End of variables */

//Get School's facebook object.
$school_id=substr($school_db['facebook_id'], strrpos($school_db['facebook_id'],'/', -2));
$school_fb=$fb->api($school_id);


// Check whether user Rated all classes that professor teached
			
$all_classes_are_rated=true;
	// Compare classes and check whether all of them are filled in
foreach ($classes as $class){

	$class_constructed=$professor_fb['id'].":".$class;
	
	
	if (strpos($user_db['rated_id'], $class_constructed)===false){
		
		// If at least one class is not rated, then don't show it
		$all_classes_are_rated=false;
	
	}
	
	

}


// Check if professor is approved
$sql="SELECT * FROM approved_professors WHERE facebook_id='".$professor_fb['id']."'";
$result=mysql_query($sql);

$approved=false;



if (mysql_fetch_array($result)){
$approved=true;

}



?>
<!-- Specify Document-based Style -->
<style>

.class-selector{
padding: 5px;
font-weight:bold;
cursor:pointer;
margin: 5px;



}

</style>


<table style="margin-left:20px">


	<tr>
    	<!-- Professor's Image -->
    	<td>
       <p> <h3><?php echo $professor_fb['name'];?> </h3><?php
	   
		// Output whether professor is Approved or not
		if ($approved==true){

			echo "<a style=\"padding:5px;font-size:13px;color:#5DA2F0;border-color:#5DA2F0;border-style:solid;border-width:1px\"> &#10003; This is approved Professor! </a>";

		} else {
		
			echo "<a style=\"padding:5px;font-size:13px;color:#E62E2E;border-color:#E62E2E;border-style:solid;border-width:1px\"> This professor is not approved yet! </a>";

			
		}

?></p>
        <img src="http://graph.facebook.com/<?php echo $professor_fb['id']; ?>/picture?width=150" />
		</td>
        
        <!-- Professor's Info -->
        <td>
        	
            <?php
			// Add edit-page button if it's an owner
			
			if ($user_fb['id']==$professor_fb['id']){
			?>	
				
                <p align="right"> <a class="small-button" onclick="$('#edit-info-dialog').dialog('open');" href="#"> Edit your page</a> </p>
                
			<?php
			}
			?>
			
        	<h1><a href="?location=university&universityid=<?php echo $school_fb['id']; ?>"> <?php echo $school_fb['name']; ?></a></h1><br/>
			
			<div> <?php echo $professor_fb['name']?> 
				
				teaches
			
				<?php 
					// Lists all classes that professor is teaching in a correct way
					$classes=explode(",", $professor_db['subjects']);
			
					// If there are more then two classes
					if (sizeof($classes)>2){
			
			
						//Loop through classes
						for ($i=0;$i<sizeof($classes);$i++){
			
							echo $classes[$i];
			
			
							// Output list as class,class,class and lastclass
							if ($i==sizeof($classes)-2){
								echo  ", and ";
			
							} else if ($i==sizeof($classes)-1){
			
							}else{
			
			
								echo ", ";
							}
			
			}
			
			// Output it ass class and class if there are only two classes
			} 
						else if (sizeof($classes)==2){
			
						echo $classes[0]." and ".$classes[1];
			
			
					// If there is only one, output it as it is
					} else {
			
						echo $classes[0];
				
					}
		
				
			
				
				?>	
			
			
			
			</div>
			
        </td>
        
	</tr>
    
    
    <tr>
    
    		<!-- Display Professors ratings-->
    		<td>
            	<!-- Display Ratings -->
                <p>Ratings</p>
                <p>Likes: <span class="rating" ><?php echo $professor_db['likes']?></p>
                <p>Dislikes: <span class="rating" ><?php echo $professor_db['dislikes']?></p>
                <p>Ratio: <span class="rating" ><?php echo round($professor_db['ratio'], 2)?></p>
               
               	<!-- Report and rate button -->
				
				<?php
				
				// If user rated all classes, do not let them rate prfoessor at all
				if ($all_classes_are_rated){
				
					echo '<span style="margin:0;padding:0;">You Already rated all classes of this professor! </span><br/>';
				
				//  Professors cannot rate professors.
				} else if ($user_db['group']=='professors') {
				
					echo '<span style="margin:0;padding:0;"> Professors cannot rate professors </span><br/>';
				
				
				} else {?>
               	<a class="small-button" href="#" onclick="$('#add-comment-dialog').dialog('open');"> Rate this Professor </a><br/>
                <?php } ?>
				
				<?php
				//Check whether user already reported this user
				
				$sql=sprintf("SELECT * FROM professors_reports WHERE author_id='%s' AND reported_id='%s'", $user_fb['id'], $professor_fb['id']);
				$result=mysql_query($sql);
				
				
				if (mysql_num_rows($result) > 0){
				
					echo '<span> You already reported this professor </span>';
				
				} else { ?>
				
					<a class="small-button" onclick="$('#report-user-dialog').dialog('open');" href="#"> Report this Professor </a><br/>
               
			   <?php } ?>
    		</td>
        
    </tr>
    
    	<tr>
    		
            <!-- Empty tab -->
    		<td>
            &nbsp;
            </td>
    
    		<!-- Display Comments to this professor -->
            <td>
            	<?php
				
				// Create a selector for classes
				foreach($classes as $class){
				
				echo sprintf('<a onclick="showCommentsForClass(\'%s\');" class="class-selector" subject="%s">%s</a>',$class, $class, $class);
				
				}
				
			
				//Select classes that professor teach 
				foreach ($classes as $class){
				?>
					
                   	<div id="comments-<?php echo $class; ?>" class="comment-block" style="padding:10px; background-color:#A0D4F8;display:none;">
                    	<?php
						// Handle 0 comments for the subject
						$comments_counter=0;
					
						// Get all comments for this class for this prfessor
						$comments_unfetched=mysql_query(sprintf("SELECT id, body, reports FROM comments WHERE professor_id='%s' AND subject='%s' ", $professor_fb['id'], $class));
						
						while ($comment=mysql_fetch_array($comments_unfetched)){
						
						if ($comment['reports']<$maximum_reports_for_comment){	
						$comments_counter++;	
						?>
                        	<div id="comment-<?php echo $comment['id']?>" style="background-color:#5C9AFD; margin:20px; border-radius:10px;width:400px;padding:10px;">
                            
                            	<?php echo str_replace("\\", "", $comment['body']); ?>
                            
                            	<?php
								if ($user_fb['id']==$professor_fb['id']){
								
									echo sprintf('<p align="right"> <a class="button-small" onclick="submitCommentReport( %s)" href="#">Report this Comment!</a> </p>', $comment['id']);
							
								}
							
								?>
                            
                            </div>
                        	
						
						<?php
							} // End of comparison of maximum amount of nodes
						} // End of while loop.
						
						
						
						if ($comments_counter==0){
						
						echo "Sorry, there are no comments for this class yet! Maybe you'll be the first one to add?";
						
						}
						
						?>
                    	
                    
                    </div>
					
                <?php    
				} // End of foreachloop
			
				?> 
            </td>
            
    	</tr>
    
    
    
    </table>
   
<!-- Dialogs -->  
    <!-- Add Comment Dialog --> 
   
    <div id="add-comment-dialog"  title="Rate this professor" style="background-color:#CCC;padding:15px;border-radius:10px;border-color:#999;border-style:solid;border-width:1px;">
   
   		<p align="left">Select your class:</p>
   
   		<!-- Display all classes -->
   		<p align="left">
        	<select id="class-to-comment">
   				<?php 
			
				//Scan through all professor's classes and check whether they were rated by user.
				foreach ($classes as $class){
				
					$fullClassName=$professor_fb['id'].":".$class;
				
					// If there is no such class, print that option
					if (strpos($user_db['rated_id'], $fullClassName)===false){
					
						echo sprintf("<option val=\"%s\">%s</option>", $class, $class);
					
					}
										
				}	
			
				?>
   		    </select>
        </p>
    
    	
        
        <!-- Choose like or dislike the professor -->
        <p align="left">
        
        	I
        	 <select id="do-you-like-professor"> 
            
            	<option value="1">like</option>
                <option value="0">don't like</option>
                
             </select>
         	this professor.
             
        </p>
        
        <!-- Textarea to edit comment -->
        <p align="left"> Write your comment: </p>
        
        <textarea rows="10" cols="80" style="resize:none;" id="comment-body" placeholder="Write your comment here... Thank you"></textarea>
        <br/>
   		<!-- Submit Button -->
        <a class="button" onclick="submitComment();" href="#"> Submit your rating </a>
   
    
    </div>
   
   
   	<!-- Dialogs that are visible only to the owner -->
    <?php if ($user_fb['id']==$professor_fb['id']){ ?>
    
    <!-- Edit porfessor's info dialog -->
    <div title="Edit your Info:" id="edit-info-dialog" style="background-color:#CCC;padding:15px;border-radius:10px;border-color:#999;border-style:solid;border-width:1px;">
    	
      
    	
       	<!-- Print all fields as list -->
        <ul class="form" style="list-style-type:none;text-align:left;">
        
        	<!-- basic info -->
        	
            
			<li id="">Classes:   <a style="font-size:10px;" onclick="addClass()">Add class </a> </li> 
			
			<?php
				// Output all classes with links to delete them
				$counter=1;
				
				foreach ($classes as $class){
				
				 echo '<li class="subjectRow"><input type="text" id="class-'.$counter.'" value="'.$class.'" class="subject-list-element" /> <a style="cursor:pointer;font-size:10px;" onclick="$(\'#class-'.$counter.'\').remove();$(this).remove() "> Remove this class </a></li>';
				
				
				}
				
			
			?>
            <!-- Selection of School -->
            
			<li> Select a new School </li>
			<li>
            	<!-- Load schools -->
            	<select id="school-change">
                	<?php
					//Get schools list from database and print eaxh as an option
					$schools_unfetched=mysql_query("SELECT name FROM schools");
					
					while ($school=mysql_fetch_array($schools_unfetched)){
						echo sprintf("<option value=\"%s\">%s</option>", $school['name'], $school['name']);
					}
					?>
                </select>
            
            </li>
           
            
        
        </ul><br/>
        
        <!-- Buttons to save changes and edit page -->
        <a class="button" style="font-size:14px;color:#C9000C;border-color:#C9000C" onclick="deleteMyPage()"> Delete my Page </a>&#12288;<a class="button" style="font-size:14px" onclick="saveChanges()"> Save Changes</a>
        <br/>
        
        
    
    </div>
    
    
    <?php } ?>
    
    
	<!-- Report User Dialog -->
    <div title="Write a Report!"id="report-user-dialog" style="background-color:#CCC;padding:15px;border-radius:10px;border-color:#999;border-style:solid;border-width:1px;">
		<p align="left">Please briefely explain us the reason:</p>
		<textarea id="user-report-description" rows="5" cols="70" placeholder="Write the reason here" ></textarea><br/>
		
		<a class="button" onclick="submitUserReport()">Submit your report</a>
    </div>
	
	
<!-- Scripts -->
    
    <script type="text/javascript">
	var commentsCounter=<?php 
	
						// Print Counter if it exists
						if (isset ($counter)){

							echo $counter;
						
						} else {

							echo 1;
						
						}	
						?>; // Holds amount of comment-fields
	
	// Initialization
	$(document).ready(function (){
	
	showCommentsForClass('<?php echo $classes[0]?>');
	
	
	
	// Handle Dialogs
	$("#add-comment-dialog").dialog({
		autoOpen: false,
		minWidth: 750,
		minHeight: 450
		});
	
	$("#report-user-dialog").dialog({
		autoOpen: false,
		minWidth: 650,
		minHeight: 250
		});
	
	<?php 
	
	
	if ($user_fb['id']==$professor_fb['id']){ ?>
	$("#edit-info-dialog").dialog({
		autoOpen: false,
		minWidth: 350,
		minHeight: 250
		});
	

   	
	$("#report-comment-dialog").dialog({
	
		autoOpen: false,
		minWidth: 750,
		minHeight: 350
	
	});
	<?php
	 }
	?>
	});
	
	
	// Submit report on a comment
	function submitCommentReport( id ){
	
	// Simply pass to ajax script
		$.ajax({
		
			type: "POST",
			url: "professor_ajax.php",
			data: {
			    action: "report_comment",
				id: id
			
			}
			
			
			
	    }).done(function (response){
		
			// Handle non-error response
			if (response == ""){
			
				$("#comment-"+id).remove();
				
				alert("Comment was Succesfully reported. Thank you for helping us to keep applicaiton out of unacceptable behavior of users!");
			
			return;
			
			
			// Handle all other responses
			} else {
			
				alert("An error has occured. Please contact support with: "+response);
			
			}
		
		
		
		});
			
	}
	
	function submitUserReport(){
	
		// Get variables
		var reason=$("#user-report-description").val();
		
		
		// User don't have to specify a reaso
		if (reason=="") {
		
			reason = "<i> no description </i>";
		
		}
		
		
		
		
		
		//Perform ajax
		$.ajax({
		
			 type: "POST",
			 url: "professor_ajax.php",
			 data: {
				
				action: "report_professor",
				id: "<?php echo $professor_fb['id']; ?>",
				reason: reason,
				author: "<?php echo $user_fb['id']; ?>",
				
				
			}
		
		}).done( function(response){
		
		if (response==""){
		
		alert("Thank you for your cooperation. The page will now be reloaded");
		
		window.location.reload();
		} else {
		
		alert(response);
		
		
		}
		
		
		});
		
		
		
	
	}
	// Submit comment by User
	function submitComment(){
	//Function to submit comments
	
	// Collect variables
	var commentBody=$('#comment-body').val();
	var like=$('#do-you-like-professor>option:selected').val();
	var subject=$('#class-to-comment>option:selected').val();
	
	
	// Try to submit a comment
	$.ajax({
	
	type: "POST",
	url: "professor_ajax.php",
	
	data: {
	action : "add_comment", 
	author: "<?php echo $user_fb['id'];?>",
	target: "<?php echo $professor_fb['id'];?>",
	body: commentBody,
	liked: like,
	subject: subject
	}
	
	}).done( function(response){
	//Handle Different Responses
	
	//If user didn't enter any comment
	if (response == 'nullfield'){
	
		alert("Please write your comment, empty comments are not acceptable");
		return;
	
	// If there wasn't response, then everything is ok
	} else  if (response == ""){
	
	
		window.location.reload();
	
	// If any other error, show it
	} else {
	
	alert(response);
	return;
	}
	
	}
	
	); // End of Ajax
	
	$('#add-comment-dialog').dialog('close');
	
	
	

	
	}
	
	// Add class 
	function addClass(){
	
		
	
		// Increment counter
		commentsCounter++;
		
		
		// '<li><input type="text" id="class-'.$counter.'" value="'.$class.'" class="subject-list-element" /> <a style="cursor:pointer;font-size:10px;" onclick="$(\'#class-'.$counter.'\').remove();$(this).remove() "> Remove this class </a></li>';
				
		
		// Construct new input field and button
		var inputField="<input type=\"text\" id=\"class-"+commentsCounter+"\" class=\"subject-list-element\" />";
		var button="<a style=\"cursor:pointer;font-size:10px;\" onclick=\"$(\'#class-"+commentsCounter+"\').remove();$(this).remove() \"> Remove this class </a>";
				
		// Add it to the screen
		
		var newHTML= "<li class=\"subjectRow\">"+inputField+button+"</li>";
		
		//alert(newHTML);
		
		$(".subjectRow:last").append(newHTML);
		
	
	}
	// Switch comments for classeds
	function showCommentsForClass(subject){
	
		// Hide all comments
		$(".comment-block").hide('fast');
	
		// Show the specified block
		$("#comments-"+subject).show('slow');
		
		//Make all unselected classes bold
		$(".class-selector").css('font-weight', 'bold');
		
		$("a[subject='"+subject+"']").css('font-weight', 'normal');
		
	
	}
	
	
	// Delete Professor's page 
	function deleteMyPage(){
	
		// Prompt confirmation
		if (confirm('Are you sure you want to delete your page? Note: This action cannot be undone')){
			
			$.ajax({
			
			type: "POST",
			url: "professor_ajax.php",
			data: {id: '<?php echo $user_fb['id'];?>', action: "delete_page"}
			
			
			}).done(function (response){
			
				// If the page was successfully deleted, prompt the dialog and reload the page
				if (response==""){
				
					alert("Your page was successfully deleted, please hold still and you'll be redirected to the home page");
					window.location.reload();
				
				// If user got another response, then something happened and ask him to contact the support.
				} else {
				
					alert(response+". Please contact support with this error.");
				
				}
			
			
			
			});
			
		
		}
	
	
	
	
	}
	
	// Update User-made changes
	function saveChanges(){
		
		var classes="";
		
		
		$(".subject-list-element").each( function(){
		
			classes+=$(this).val()+",";
		
		});
		
		if (classes.length==0){
			
				alert("You cannot have zero classes");
				return;
			
		}
		
		classes=classes.substring(0, classes.length-1);
		

		// Update Database and reload page
		
		$.ajax ({
		
		type: "POST",
		url: "professor_ajax.php",
		data: {action: "update_profile" , subjects: classes, school_name:$('#school-change>option:selected').val(), user_id: "<?php echo $user_fb['id']; ?>"}
			
			
		
		}).done(function (response){
		
		
		// Handle correct response
		if (response == ""){
			
			alert("You have Succesfully changed your profile, the page will now be reloaded");
			window.location.reload();
			
		
		// Handle incorrect response
		} else {
		
			alert ("An error was found. Please contact the support with the following:" + response);
			return;
		}
		
		});
		
		
	
	}
	
	
	
	</script>