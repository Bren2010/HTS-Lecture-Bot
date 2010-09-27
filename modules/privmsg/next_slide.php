
return function($message) {
	global $accessArray;
	global $nick;
	
	$hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
	
	$search = searchAccess($hostmask, $accessArray);
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
	$text = trim($parameters[1]);
	
	if ($search !== FALSE && $where == $nick && $text == "n") {
		global $initiated;
		
		if ($initiated == TRUE) {
			global $channel;
			global $position;
			global $lecture;
			
			if (!empty($lecture[$position])) {
				talk($channel, $lecture[$position]);
				say("Played slide " . $position . ".");
				$position++;
			} else {
				say("Out of slides.");
			}
		} else {
			say("Please initiate a lecture to show slides.");
		}
	}
}
?>
