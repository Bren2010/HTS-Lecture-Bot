//<?php
return function($data) {
	if ($data['command'] == "nick") {
		// Start of variable declaration to cover from the transition.
		$message = $data['message'];
		
		$nick = $message->getNick();
		$command = strtolower($message->getCommand());
		$parameters = $message->getParameters();
		
		$text = "";
		// End.
		
		$text = "* " . $message->getNick() . " is now known as " . trim($parameters[0]) . ".";

		// Start of functions copyandpasta to cover from the transition.
		if ($text != "") {
			echo time() . " " . $text . "\n";
		}
		// End.
	}
}
?>
