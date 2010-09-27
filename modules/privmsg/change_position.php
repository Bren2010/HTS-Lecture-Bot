
return function($message) {
	global $accessArray;
	global $nick;
	
	$hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
	
	$search = searchAccess($hostmask, $accessArray);
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
	$command = substr(trim($parameters[1]), 0, 1);;
	
	if ($search !== FALSE && $where == $nick && $command == "c") {
		global $initiated;
		
		if ($initiated == TRUE) {
			global $channel;
			global $position;
			global $lecture;
			
			$realPos = trim(substr($parameters[1], 1));
			
			if (!empty($lecture[$realPos])) {
				$position = $realPos;
				say("Changed slide position to " . $realPos . ".");
			} else {
				say("The requested slide could not be found.");
			}
		} else {
			say("Please initiate a lecture to show slides.");
		}
	}
}
?>
