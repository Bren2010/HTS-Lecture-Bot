
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
	
    if ($where != $nick) return;
    
    $hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
    if (!searchAccess($hostmask, $accessArray)) return;
	
	if ($parameters[1][0] != 'c') return;
    
    if (!$initiated) 
        return say("Please initiate a lecture to show slides.");
    
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
};
?>
