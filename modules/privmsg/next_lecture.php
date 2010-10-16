//<?php
return function($message) {
	global $nick;
	
	$parameters = $message->getParameters();
	
	$preWhere = $parameters[0];
	
	if ($preWhere == $nick) {
		$where = $message->getNick();
	} else {
		$where = $preWhere;
	}
	
	$talk = trim($parameters[1]);
	$goodText = $nick . ", next lecture?";
	
	if ($talk == $goodText) {
		global $initiated;
		
		if (!$initiated) {
			global $dbUsername;
			global $dbPassword;
			global $dbHostname;
			global $dbDatabase;
			
			$dbc = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbDatabase);
			
			if ($dbc) {
				$query = mysqli_query($dbc, "SELECT * FROM lectures WHERE time > " . time() . " ORDER BY  time ASC LIMIT 1");
				$rows = mysqli_num_rows($query);
				
				if ($rows > 0) {
					while ($array = mysqli_fetch_array($query)) {
						// Countdown
						$timestamp = $array['time'] - time();
						
						// A lot of the below is overly complicated fail math.... fyi. <3
						$days = (int) round($timestamp/86400, 0);
						$hours = (int) round(($timestamp - ($days * 86400))/3600, 0);
						$minutes = (int) round(($timestamp - (($hours * 3600) + ($days * 86400)))/60, 0);
						
						
						talk($where, "There is one upcomimg lecture on '" . $array['title'] . "' by " . $array['lecturers'] . " in " . $days . " days, " . $hours . " hours, and " . $minutes . " minutes.\nDescription: " . $array['description'] . "\nLink: http://www.hackthissite.org/forums/" . $array['link']);
					}
				} else {
					talk($where, "There are no upcoming lectures! Sorry. :(");
				}
			} else {
				say("ERROR: Could not connect to database.");
				cmd_send("NOTICE " . $message->getNick() . " :An error has occured.  Please try again later.");
			}
		} else {
			cmd_send("NOTICE " . $message->getNick() . " :There is currently a lecture.");
		}
	}
}
?>
