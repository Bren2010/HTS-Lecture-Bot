
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
    
    if ($where != $nick) return;
    
    $hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
    if (!searchAccess($hostmask, $accessArray)) return;
	
	if ($parameters[1][0] != 'l') return;
    
    if (!$initiated) 
        return say("Please initiate a lecture to use this command.");
	
	
	global $mode;
			
	if ($mode == "q") {
		global $channel;
		global $lector;
        
		$mode = "l";
				
		cmd_send("MODE " . $channel . " +m");
		talk($channel, "The lecture is now resuming, so please end any off topic discussions and redirect your attention back to " . $lector . ".");
		say("The lecture is now in lecture mode.");
    }
};
?>
