
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
	
    if ($where != $nick) return;
    
    $hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
    if (!searchAccess($hostmask, $accessArray)) return;
	
	if ($parameters[1][0] != 'e') return;
    
    if (!$initiated) 
        return say("No lecture has been initiated.");

		global $mode;
		global $channel;
		global $intro;
		global $rules;
		
		$initiated = FALSE;
		$mode = "q";
			
		cmd_send("MODE " . $channel . " -m"); 
		talk($channel, "The lecture has come to an end.  I hope you enjoyed it and learned something new!");
		say("The lecture has been ended.");
		
		echo ("<!-- LECTURE ENDS HERE -- LECTURE ENDS HERE -->\n\n");
};
?>
