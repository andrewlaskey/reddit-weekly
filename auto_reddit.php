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
		if ($title == "news") {
			$submissions = array();

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/Foodforthought/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/news/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/politics/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/science/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/technology/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/TrueReddit/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/worldnews/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);
		} else {
			$submissions = array();

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/alltheleft/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/cooperatives/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/economicdemocracy/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/GreenParty/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/labor/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/Liberal/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);

			$reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/SocialDemocracy/top/.json?limit=2&t=week"));
			$submissions = array_merge($submissions, $reddit_data->data->children);
		}

		//Get the list of subscribers and ship out
		require("db_info.php");

		// Opens a connection to a MySQL server
		$connection = mysql_connect ("localhost", $username, $password);

		if ($connection) {

			// Set the active MySQL database
			$db_selected = mysql_select_db($database, $connection);

			if ($db_selected) {

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
							echo $link->data->url;
						}

				  		mail($to,$subject,$message,$headers);
				  	}
				}//End sql result

			}//End db select
		}//End connection check
	}//End Send Mail


?>