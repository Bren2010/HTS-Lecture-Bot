
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
    
    if ($where != $nick) return;
    
    $hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
    if (!searchAccess($hostmask, $accessArray)) return;
	
	if ($parameters[1][0] != 'i') return;
    
    if ($initiated) 
        return say("The lecture has already been initiated.");
    
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
};
?>
