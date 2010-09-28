
return function ($message) {
	global $nick;
	
	$parameters = $message->getParameters();
	
	$where = $parameters[0];
	unset($parameters[0]);
	$msg = trim(implode(" ", $parameters));
	
	if ($where == $nick && $msg == "quit") {
		global $accessArray;
		
		$hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
		
		$search = searchAccess($hostmask, $accessArray);
		
		if ($search !== FALSE) {
			$level = $accessArray[$search]['level'];
			
			if ($level == '2') { // <-- Requires operator class
				cmd_send("QUIT :Ordered by " . $message->getNick());
				exit();
			}
		}
	}
}
?>
