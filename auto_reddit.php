<?php

	$today = date('w');

	if ($today == 1) {
		$title = "News";
		$sendmail = true;
	} elseif ($today == 3) {
		$title = "Politics";
		$sendmail = true;
	}

	if ($sendmail) {
		require('db_info.php');

		// Opens a connection to a MySQL server
		$connection = mysql_connect ('localhost', $username, $password);
		if (!$connection) {
			//die('Not connected : ' . mysql_error());
			//die('Something went wrong. Please click your browsers "back" button. Thank you.');

			// Set the active MySQL database
			$db_selected = mysql_select_db($database, $connection);
			if (!$db_selected) {
				//die ('Can\'t use db : ' . mysql_error());
				//die('Something went wrong. Please click your browsers "back" button. Thank you.');


			}
		}
	}

?>