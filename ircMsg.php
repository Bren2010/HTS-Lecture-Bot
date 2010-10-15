<?php

class ircMsg
{
    private $raw;
    private $nick;
    private $name;
    private $host;
    private $command;
    private $parameters = array();
    
    public function __construct($message)
    {
        $this->parse($message);
    }
    
    public function getRaw()
    {
        return $this->raw;
    }
    
    public function getNick()
    {
        return $this->nick;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getHost()
    {
        return $this->host;
    }
    
    public function getCommand()
    {
        return $this->command;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
    private function parse($preMessage)
    {
        $this->raw = $preMessage;
        
        $message = substr($preMessage, 1);
        $array = explode(" ", $message);
        
        // Define the constants.
        $id = $array[0];
        $command = $array[1];
        
        $this->command = $command;
        
        $idArray = explode("@", $id);
        $userArray = explode("!", $idArray[0]);
        
        if (isset($idArray[1])) {
			$this->nick = $userArray[0];
			$this->name = $userArray[1];
			$this->host = $idArray[1];
			$improv = FALSE;
		} else {
			$this->nick = $idArray[0];
			$this->name = $idArray[0];
			$this->host = $idArray[0];
			$improv = TRUE;
		}
        
        $delimiter = strpos($message, " :");
        
        if ($delimiter == FALSE) {
			$tempParameters = $array;
			
			unset($tempParameters[0]);
			unset($tempParameters[1]);
			
			$tempParams = $tempParameters;
		} else {
			$delMessage = substr($message, $delimiter + 2);
			
			if ($improv == FALSE) {
				$length = strlen($this->nick . "!" . $this->name . "@" . $this->host . " " . $this->command);
			} else {
				$length = strlen($this->nick . " " . $this->command);
			}
			$remainingParameters = trim(substr($message, $length, $delimiter - $length));
			
			$tempParams = explode(" ", $remainingParameters);
			array_push($tempParams, $delMessage);
		}
		$this->parameters = array_merge(array(), array_filter($tempParams));
    }
}
