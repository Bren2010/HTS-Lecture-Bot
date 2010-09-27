
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];

	$hostmask = $message->getHost();
	
	$search = searchAccess($hostmask, $accessArray);
	
	$text = trim($parameters[1]);		
				
	if ($where == $nick && $search !== FALSE && $text == "e") {

		if ($initiated == TRUE) {
			global $mode;
			global $channel;
			global $intro;
			global $rules;
			
			$initiated = FALSE;
			$mode = "q";
			
			cmd_send("MODE " . $channel . " -m");
			talk($channel, "The lecture has come to an end.  I hope you enjoyed it and learned something new!");
			say("The lecture has been ended.");
		} else {
			say("No lecture has been initiated.");
		}
	}
}
?>
