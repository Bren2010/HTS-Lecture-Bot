
return function($message) {
	global $accessArray;
	global $nick;
	
	$raw = $message->getRaw();
	$hostmask = substr($raw, 1, strpos($raw, " ") - 1);
	
	$search = searchAccess($hostmask, $accessArray);
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
	$command = substr(trim($parameters[1]), 0, 1);;
	
	if ($search !== FALSE && $where == $nick && $command == "s") {
		global $initiated;
		
		if ($initiated == TRUE) {
			global $channel;
			global $position;
			global $lecture;
			
			$realPos = trim(substr($parameters[1], 1));
			
			if (!empty($lecture[$realPos])) {
				talk($channel, $lecture[$realPos]);
				say("Played slide " . $realPos . " on request.");
			} else {
				say("The requested slide could not be found.");
			}
		} else {
			say("Please initiate a lecture to show slides.");
		}
	}
}
?>
