//<?php
return function($data) {
	if ($data['command'] == "part") {
		// Start of variable declaration to cover from the transition.
		$message = $data['message'];
		
		$nick = $message->getNick();
		$command = strtolower($message->getCommand());
		$parameters = $message->getParameters();
		
		$text = "";
		// End.
		
		$channel = $parameters[0];
		unset($parameters[0]);
		$reason = trim(implode(" ", $parameters));
		
		$text = "* " . $nick . " has left " . $channel . " (" . $reason . ")";

		// Start of functions copyandpasta to cover from the transition.
		if ($text != "") {
			echo time() . " " . $text . "\n";
		}
		// End.
	}
}
?>
