<?php
/**
* This script just logins to the facebook and db;
*
*/



include "mysql.php";

include "facebook_login.php";
  //Javascript login
  
 echo " 
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '".$facebook_appID."', // App ID
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });

    // Additional initialization code here
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = \"//connect.facebook.net/en_US/all.js\";
     ref.parentNode.insertBefore(js, ref);
   }(document));
</script>
<div id=\"fb-root\"></div>
";

?>