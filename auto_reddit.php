<?php

	$today = date("w");

	if ($today == 1) {
		$title = "news";
		$sendmail = true;
	} elseif ($today == 3) {
		$title = "politics";
		$sendmail = true;
	}

	if ($sendmail) {
		//Compile the list

		//Get the list of subscribers and ship out
		require("db_info.php");

		// Opens a connection to a MySQL server
		$connection = mysql_connect ("localhost", $username, $password);
		if (!$connection) {
			//die("Not connected : " . mysql_error());
			//die("Something went wrong. Please click your browsers "back" button. Thank you.");

			// Set the active MySQL database
			$db_selected = mysql_select_db($database, $connection);
			if (!$db_selected) {
				//die ("Can\"t use db : " . mysql_error());
				//die("Something went wrong. Please click your browsers "back" button. Thank you.");

				$sql = sprintf("SELECT * FROM email_list WHERE '%s' = 1",
					mysql_real_escape_string($title));

				$result = mysql_query($sql);
	
				if (!$result) {
					//die("Invalid query: " . mysql_error());
					//die("Something went wrong. Please click your browsers "back" button. Thank you.");
					while($row = mysql_fetch_array($result))
				  	{
				  		$to = $row["subscriber_email"];
				  		$from = "autoreddit@andrewlaskey.com";
						$headers  = "From: $from\r\n";
					    $headers .= "Content-type: text/html\r\n"; 
				  		$subject = "Weekly Reddit Newsletter - " . $title;
				  		$message = "<h1>This week's top " . $title . " links:</h1>";

				  		mail($to,$subject,$message,$headers);
				  	}
				}

			}//End db select
		}
	}//End connection check

?>