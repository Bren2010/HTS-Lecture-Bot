return function($message) {
	global $nick;
	global $channel;
	
	$parameters = $message->getParameters();
	
	if ($parameters[1] == $nick) {
			cmd_send("JOIN " . $channel);
	}
}
?>
