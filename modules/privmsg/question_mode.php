
return function($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];

	$raw = $message->getRaw();
	$hostmask = substr($raw, 1, strpos($raw, " ") - 1);
	
	$search = searchAccess($hostmask, $accessArray);
	
	$text = trim($parameters[1]);		
				
	if ($where == $nick && $search !== FALSE && $text == "q") {

		if ($initiated == TRUE) {
			global $mode;
			
			if ($mode == "l") {
				global $channel;
				
				$mode = "q";
				
				cmd_send("MODE " . $channel . " -m");
				talk($channel, "The channel has now been opened for any questions you wish to ask the lecturer.  I know you have questions, so don't be afraid to ask on Ventrillo or IRC.\nYou can also consider this intermission if the lecturer is taking a break or if you're too cool for questions.");
				say("The lecture is now in question mode.");
			} else {
				say("The lecture is already in question mode.");
			}
		} else {
			say("Please initiate a lecture to use this command.");
		}
	}
}
?>
