
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
				
	
	if ($mode == "l") {
		global $channel;
		
        $mode = "q";
				
		cmd_send("MODE " . $channel . " -m");
		talk($channel, "The channel has now been opened for any questions you wish to ask the lecturer.  I know you have questions, so don't be afraid to ask on Ventrillo or IRC.\nYou can also consider this intermission if the lecturer is taking a break or if you're too cool for questions.");
		say("The lecture is now in question mode.");
	} else {
		say("The lecture is already in question mode.");
	}
};
?>
