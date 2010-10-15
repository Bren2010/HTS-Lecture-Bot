
return function ($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
    
    if ($where != $nick) return;
    
    $hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
    $search = searchAccess($hostmask, $accessArray);
    
    if (!$search) return;
    
    if ($level = $accessArray[$search]['level'] != 2) return;
	
    $msg = trim($parameters[1]);
    
	if ($msg != 'reload lecture') return;
	
	global $lecture;
	global $position;
				
	$lecture = explode("\n\n", trim(file_get_contents("lecture.txt")));
	$position = 0;
			
	say("The lecture has been reloaded and slide position set to 0.");
};
?>
