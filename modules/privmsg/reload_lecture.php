
return function ($message) {
	global $nick;
	
	$parameters = $message->getParameters();
	
	$where = $parameters[0];
	unset($parameters[0]);
	$msg = trim(implode(" ", $parameters));
	
	if ($where == $nick && $msg == "reload_lecture") {
		global $accessArray;
		
		$hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
		
		$search = searchAccess($hostmask, $accessArray);
		
		if ($search !== FALSE) {
			$level = $accessArray[$search]['level'];
			
			if ($level == '2') { // <-- Requires operator class
				global $lecture;
				global $position;
				
				$lecture = explode("\n\n", trim(file_get_contents("lecture.txt")));
				$position = 0;
				
				say("The lecture has been reloaded and slide position set to 0.");
			}
		}
	}
}
?>
