
return function($message) {
	global $accessArray;
	global $nick;
	
	$raw = $message->getRaw();
	$hostmask = $message->getHost();
	
	$search = searchAccess($hostmask, $accessArray);
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
	$text = trim($parameters[1]);
	
	if ($search !== FALSE && $where == $nick && $text == "p") {
		global $initiated;
		
		if ($initiated == TRUE) {
			global $channel;
			global $position;
			global $lecture;
			
			$realPos = $position - 2;
			
			if (!empty($lecture[$realPos])) {
				talk($channel, $lecture[$realPos]);
				say("Played slide " . $realPos . ".");
			} else {
				say("There is no previous slide.");
			}
		} else {
			say("Please initiate a lecture to show slides.");
		}
	}
}
?>
