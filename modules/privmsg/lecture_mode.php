
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];

	$hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
	
	$search = searchAccess($hostmask, $accessArray);
	
	$text = trim($parameters[1]);		
				
	if ($where == $nick && $search !== FALSE && $text == "l") {

		if ($initiated == TRUE) {
			global $mode;
			
			if ($mode == "q") {
				global $channel;
				global $lector;
				
				$mode = "l";
				
				cmd_send("MODE " . $channel . " +m");
				talk($channel, "The lecture is now resuming, so please end any off topic discussions and redirect your attention back to " . $lector . ".");
				say("The lecture is now in lecture mode.");
			} else {
				say("The lecture is already in lecture mode.");
			}
		} else {
			say("Please initiate a lecture to use this command.");
		}
	}
}
?>
