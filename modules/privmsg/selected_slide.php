
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
    
    if ($where != $nick) return;
    
    $hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
    if (!searchAccess($hostmask, $accessArray)) return;
	
	if ($parameters[1][0] != 's') return;
    
    if (!$initiated) 
        return say("Please initiate a lecture to use this command.");
	
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
};
?>
