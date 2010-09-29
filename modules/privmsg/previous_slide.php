
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
    
    if ($where != $nick) return;
    
    $hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
    if (!searchAccess($hostmask, $accessArray)) return;
	
	if ($parameters[1][0] != 'p') return;
    
    if (!$initiated) 
        return say("Please initiate a lecture to show slides.");
	
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
};
?>
