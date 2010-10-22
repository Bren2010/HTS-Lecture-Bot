//<?php
return function($data) {
	if ($data['command'] == "kick") {
		// Start of variable declaration to cover from the transition.
		$message = $data['message'];
		
		$nick = $message->getNick();
		$command = strtolower($message->getCommand());
		$parameters = $message->getParameters();
		
		$text = "";
		// End.
		
		$where = $parameters[0];
		$who = $parameters[1];
		
		unset($parameters[0]);
		unset($parameters[1]);
		
		$reason = trim(implode(" ", $parameters));
		
		$text = "* " . $who . " has been kicked from " . $where . " by " . $nick . " (" . $reason . ")";
		
		// Start of functions copyandpasta to cover from the transition.
		if ($text != "") {
			echo time() . " " . $text . "\n";
		}
		// End.
	}
}
?>
