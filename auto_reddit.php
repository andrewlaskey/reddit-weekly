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

		//Get the list of subscribers and ship out
		require("db_info.php");

		// Opens a connection to a MySQL server
		$connection = mysql_connect ("localhost", $username, $password);

		if ($connection) {

			// Set the active MySQL database
			$db_selected = mysql_select_db($database, $connection);

			if ($db_selected) {

				//Compile the list
				$submissions = array();

				$sql = sprintf("SELECT * FROM topics WHERE topic = '%s'",
					mysql_real_escape_string($title));

				$result = mysql_query($sql);

				if ($result) {

					while($row = mysql_fetch_array($result))
						{
							$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/" . $row["reddit"] . "/top/.json?limit=2&t=week"));
							$submissions = array_merge($submissions, $reddit_data->data->children);
						}

				}

				//Get Subscriber List and mail out
				$sql = sprintf("SELECT * FROM email_list WHERE %s = 1",
					mysql_real_escape_string($title));

				$result = mysql_query($sql);
	
				if ($result) {

					while($row = mysql_fetch_array($result))
				  	{
				  		$to = $row["subscriber_email"];
				  		$from = "autoreddit@andrewlaskey.com";
						$headers  = "From: $from\r\n";
					    $headers .= "Content-type: text/html\r\n"; 
				  		$subject = "Weekly Reddit Newsletter - " . $title;
				  		$message = "<h1>This week's top " . $title . " links:</h1>";

				  		foreach ($submissions as $link) {
							$message.= "<p>" . $link->data->title . "<br />";
							$message.= $link->data->url . "</p>";
						}

				  		mail($to,$subject,$message,$headers);
				  	}

				}//End sql result

			}//End db select

		}//End connection check

	}//End Send Mail


?>