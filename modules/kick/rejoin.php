//<?php
return function($message) {
	global $nick;

	$parameters = $message->getParameters();
	
	if ($parameters[1] == $nick) {
			cmd_send("JOIN :" . $parameters[0]);
	}
}
?>
