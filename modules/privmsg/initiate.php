
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];

	$hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
	
	$search = searchAccess($hostmask, $accessArray);
	
	$text = trim($parameters[1]);		
				
	if ($where == $nick && $search !== FALSE && $text == "i") {

		if ($initiated == FALSE) {
			global $mode;
			global $position;
			global $channel;
			global $intro;
			global $rules;
			global $startTime;
			
			$initiated = TRUE;
			$mode = "l";
			$position = 0;
			$startTime = time();
			
			cmd_send("MODE " . $channel . " +m");
			talk($channel, $intro . "\n" . $rules);
			say("The lecture has been initiated.");
		} else {
			say("The lecture has already been initiated.");
		}
	}
}
?>
