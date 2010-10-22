//<?php
return function($data) {
	if ($data['command'] == "quit") {
		// Start of variable declaration to cover from the transition.
		$message = $data['message'];
		
		$nick = $message->getNick();
		$command = strtolower($message->getCommand());
		$parameters = $message->getParameters();
		
		$text = "";
		// End.
		
		$reason = trim($parameters[0]);
		
		$record = TRUE;
		$text = "* " . $nick . " has quit (" . $reason . ")";

		// Start of functions copyandpasta to cover from the transition.
		if ($text != "") {
			echo time() . " " . $text . "\n";
		}
		// End.
	}
}
?>
