return function($data) {
	global $record;
	global $initiated;
	
	if ($record == TRUE && $initiated == TRUE && $data['action'] == "privmsg") {
		global $lectureHandle;
		global $startTime;
		
		$action = $data['action'];
		
		$time = format(time() - $startTime);
		echo ("\n\n" . $action . "\n\n");
		switch ($action) {
			case "join":
			echo ("JOINGINGGJKDJAL OMGFD JOING");
				$text = $time . " * " . $data['who'] . " has joined " . $data['where'];
				break;
				
			case "kick":
				$text = $time . " * " . $data['person'] . " has been kicked from " . $data['where'] . " by " . $data['who'] . " (" . $data['reason'] . ")";
				break;
				
			case "privmsg":
				global $nick;
				
				if ($data['where'] != $nick) {
					$text = $time . " <" . $data['who'] . "> " . $data['message'];
				} else {
					$text = "";
				}
				break;
				
			default:
				$text = "";
		}
		
		fwrite($lectureHandle, $text . "\n");
		
	}
}
?>
