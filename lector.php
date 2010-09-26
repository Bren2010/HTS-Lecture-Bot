 <?php
/*
Title: HTS Lector Bot
Developer: Brendan Mc. (Bren2010)
Purpose: To record/give lectures via IRC for HTS

Date Started: Tuesday, September 7, 2010
Date Finished: Sunday, September 12, 2010
Version: 2.0
*/
/*************** CONFIGURATION ***************/
// NickServ Interface Configuration
$nick = "Lector";
$registered = TRUE;
$password = "d|g!74ldrp3pper2012";
$lector = "Romnous";
$oper = "Bren2010";
$accessArray = array(array('level' => 2, 'hostmask' => 'Bren2010!Bren2010@bren2010.com'));

$server = "irc.hackthissite.org";
$port = "6667";
$channel = "#bren2010";

$intro = "The lecture is about to begin, so the channel will be muted until the speaker periodically unmutes it for questions.  Our speaker today is " . $lector . ", and I hope you enjoy the lecture!";
$postIntro = "There is currently a lecture proceeding in " . $channel . ".  If you don't understand, it's fine;  the lecture recording will be posted on the site so you can view it in it's entirety later.  Please make sure you have also joined the Ventrillo the lecture is currently being discussed in as well!\nVentrillo can be downloaded at http://www.ventrilo.com/download.php and you should connect to vent.i-cog.org:9765.";
$rules = "Any flooding, trolling, flaming, or interuption will result in a verbal warning.  If it continues, you will be kick banned from the channel.  If you feel a ban should be repealed, contact Monica."; 

// System Settings
$daemon = FALSE; // Run the bot as a daemon.
$output = TRUE; // Whether or not to output data sent to it.
$record = FALSE; // Used for recording the lecture.

/******************* CODE ********************/
if ($daemon == TRUE) {
	if(pcntl_fork()) die(); // This turns the bot into a daemon.
}

set_time_limit(0); // So PHP never times out
require_once("functions.php");

$lecture = explode("\n", trim(file_get_contents("lecture.txt")));

$position = 0;
$mode = "q";
$initiated = FALSE;

$startTime = time();
$lectureHandle = fopen("recording.log", "w");

$socket = fsockopen($server, $port);
cmd_send("USER " . $nick . " " . $nick . " " . $nick . " : " . $nick); // Register user data.
cmd_send("NICK " . $nick); // Change nick.
			
cmd_send("JOIN " . $channel); // Join default channel.

while (1) {
	while ($data = fgets($socket)) {
		if ($output == TRUE) {
			echo $data;
		}
		
		$pingCheck = substr($data, 0, strlen("PING :"));
		
		if ($pingCheck == "PING :") {
			cmd_send("PONG :" . substr($data, $pingCheck + strlen("PING :")));
		} else {
			$colon = strpos($data, " :");
			
			if (!empty($colon)) {
				$ircData = substr($data, 0, $colon);
				$message = trim(substr($data, $colon + 2));
			} else {
				$ircData = substr($data, 0);
				$message = "";
			}
		
			$remainingData = explode(" ", substr($ircData, 1));

			$hostmask = $remainingData[0];
			$action = $remainingData[1];
			
			switch ($action) {
				case "PRIVMSG": // Primarily for parsing commands and can be used for logging the user in.
					$who = substr($hostmask, 0, strpos($hostmask, "!"));
					$where = $remainingData[2];
					
					if ($where == $nick) { // For issuing commands
						$search = searchAccess($hostmask, $accessArray);
						
						if ($search !== FALSE) { // This user is allowed to execute commands.
							$tempArray = array_filter(explode(" ", $message));
							
							$command = $tempArray[0];
							unset($tempArray[0]);
							$paramArray = $tempArray;
							
							switch (true) {
								case $command == "i" && $accessArray[$search]['level'] >= 1: // Initiates the lecture (Level 1 'lecturer' required.)
									$initiated = TRUE;
									$mode = "l";
									$position = 0;
									
									cmd_send("MODE " . $channel . " +m");
									talk($channel, $intro . "\n" . $rules);
										
									$time = format(time() - $startTime);
									fwrite ($lectureHandle, "" . $time . " <" . $nick . "> " . $intro . "\n");
									fwrite ($lectureHandle, "" . $time . " <" . $nick . "> " . $rules . "\n");
										
									say("The lecture has been started.");
									break;
									
								case $command == "n" && $accessArray[$search]['level'] >= 1: // Plays the next slide (Level 1 'lecturer' required.)
									if (isset($lecture[$position]) && $initiated == TRUE) {
										say("Playing slide " . $position);
										talk($channel, $lecture[$position]);
										
										
										$time = format(time() - $startTime);
										fwrite ($lectureHandle, "" . $time . " <" . $nick . "> " . $lecture[$position] . "\n");
										
										$position++;
									} else {
										say("Out of slides.");
									}
									break;
									
								case $command == "p" && $accessArray[$search]['level'] >= 1: // Plays previous slide (Level 1 'lecturer' required.)
									$realPos = $position - 2;
									
									if (isset($lecture[$realPos]) && $initiated == TRUE) {
										say("Playing slide " . $realPos);
										talk($channel, $lecture[$realPos]);
										
										
										$time = format(time() - $startTime);
										fwrite ($lectureHandle, "" . $time . " <" . $nick . "> " . $lecture[$realPos] . "\n");
										
									} else {
										say("There is no previous slide.");
									}
									break;
									
								case $command == "s" && $accessArray[$search]['level'] >= 1: // Plays selected slide (Level 1 'lecturer' required.)
									$slide = trim(substr($message, 1));

									if ($slide !== NULL && ctype_digit($slide) && $initiated == TRUE) {
										if (isset($lecture[$slide])) {
											say("Playing slide " . $slide . " on request.");
											talk($channel, $lecture[$slide]);
											
										
											$time = format(time() - $startTime);
											fwrite ($lectureHandle, "" . $time . " <" . $nick . "> " . $lecture[$slide] . "\n");
										
										} else {
											say("Invalid slide number.\n");
										}
									} else {
										say("Command 's' requires a numeric slide number to play.");
									}
									break;
									
								case $command == "c" && $accessArray[$search]['level'] >= 1: // Changes slide position (Level 1 'lecturer required.)
									$pos = trim(substr($message, 1));

									if ($pos !== NULL && ctype_digit($pos)) {
										if (isset($lecture[$pos])) {
											$position = $pos;
											say("Slide position changed to " . $pos);
										} else {
											say("There isn't a slide assigned that number.");
										}
									} else {
										say("Command 'c' requires a number to change the slide position to.");
									}
									break;
									
								case $command == "l" && $accessArray[$search]['level'] >= 1: // Changes to lecture mode (Level 1 'lecturer' required.)
									if ($mode == "q" && $initiated == TRUE) {
										$mode = "l";
										cmd_send("MODE " . $channel . " +m");
										talk($channel, "The lecture is now resuming.");
										
										
										$time = format(time() - $startTime);
										fwrite ($lectureHandle, "" . $time . " <" . $nick . "> The lecture is now resuming.\n");
										
										say("The lecture is now in lecture mode.");
									} else {
										say("The lecture is already in lecture mode.");
									}
									break;
									
								case $command == "q" && $accessArray[$search]['level'] >= 1: // Changes to question mode (Level 1 'lecturer' required.)
									if ($mode == "l" && $initiated == TRUE) {
										$mode = "q";
										
										cmd_send("MODE " . $channel . " -m");
										talk($channel, "You may now ask questions if you wish.  This may also be considered intermission if the lector is taking a break.");
										
										
										$time = format(time() - $startTime);
										fwrite ($lectureHandle, "" . $time . " <" . $nick . "> You may now ask questions if you wish.  This may also be considered intermission if the lector is taking a break.\n");
										
										say("The lecture is now in question mode.");
									} else {
										say("The lecture is already in question mode.");
									}
									break;
									
								case $command == "e" && $accessArray[$search]['level'] >= 1: // Ends lecture (Level 1 'lecturer' required.)
									if ($initiated == TRUE && $initiated == TRUE) {
										$initiated = FALSE;
										$mode = "q";
										$record = FALSE;
										
										cmd_send("MODE " . $channel . " -m");
										talk($channel, "The lecture has come to an end.  I hope you've enjoyed it!");
										
										$time = format(time() - $startTime);
										fwrite ($lectureHandle, "" . $time . " <" . $nick . "> The lecture has come to an end.  I hope you enjoyed it!\n");
										
										say("The lecture has been ended.");
									} else {
										say("You have not initiated a lecture.");
									}
									break;
							}
						}
					} elseif ($where == $channel && $initiated == TRUE) { // Recording
						$time = format(time() - $startTime);
						fwrite ($lectureHandle, "" . $time . " <" . $who . "> " . $message . "\n");
					}
					break;
					
				case "NOTICE": // Used only for logging the user in.
					$who = substr($hostmask, 0, strpos($hostmask, "!"));
					
					if ($registered == TRUE && $who == "NickServ" && $message == "If you do not change within one minute, I will change your nick.") {
						cmd_send("PRIVMSG NickServ :IDENTIFY " . $password);
					}
					break;
					
				case "JOIN": // Used for informing people that join during a lecture.
					$who = substr($hostmask, 0, strpos($hostmask, "!"));
					
					if ($who !== $nick && $initiated == TRUE) {
						talk($who, $postIntro);
						talk($who, $rules);
					}
					break;
					
				case "KICK": // Used for rejoining the channel if kicked.
					$who = $remainingData[3];
					
					if ($who == $nick) {
						cmd_send("JOIN " . $channel);
					}
					break;
			}
		}
	}
}
?>
