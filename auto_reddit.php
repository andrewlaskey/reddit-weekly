<?php

    require("db_info.php");

    // Opens a connection to a MySQL server
    $connection = mysql_connect ("localhost", $username, $password);

    if (!$connection) {
        die("Connection Error");
    }

    // Set the active MySQL database
    $db_selected = mysql_select_db($database, $connection);

    if (!$db_selected) {
        die("Can't find database");
    }

    //Check db to see if sending anything out today
    $today = date("w");

    $sql = sprintf("SELECT * FROM calendar WHERE run_day = %s",
        mysql_real_escape_string($today));

    $result = mysql_query($sql);

    if (!$result) {
        die("No query results");
    }

    while($row = mysql_fetch_array($result))
    {
        $title = $row["topic"];

        //Compile the list of top 2 submissions for each sub reddit in past week
        $submissions = array();

        $sql = sprintf("SELECT * FROM topics WHERE topic = '%s'",
            mysql_real_escape_string($title));

        $subreddits = mysql_query($sql);

        if ($subreddits) {

            while($sub = mysql_fetch_array($subreddits))
            {
                $reddit_data = json_decode(file_get_contents("http://www.reddit.com/r/" . $sub["reddit"] . "/top/.json?limit=2&t=week"));
                $submissions = array_merge($submissions, $reddit_data->data->children);
            }
        }

        //Get Subscriber List and mail out
        $sql = sprintf("SELECT * FROM email_list WHERE %s = 1",
            mysql_real_escape_string($title));

        $subscribers = mysql_query($sql);

        if ($subscribers) {

            while($subscriber = mysql_fetch_array($subscribers))
            {
                $to = $subscriber["subscriber_email"];
                $from = "autoreddit@andrewlaskey.com";
                $headers  = "From: $from\r\n";
                $headers .= "Content-type: text/html\r\n"; 
                $subject = "Weekly Reddit Newsletter - " . $title;
                $message = "<h1>This week's top " . $title . " links:</h1>";

                foreach ($submissions as $link) {
                    $message.= "<p>" . $link->data->title . "<br />";
                    $message.= $link->data->url;
                    $message.= "</p>";
                }

                mail($to,$subject,$message,$headers);
            }

        }

    }

?>