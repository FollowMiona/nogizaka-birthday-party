<?php

class ics_events
{
	const DT_FORMAT = 'Ymd\THis'; //define date format here
	public $events;

	function ics_events($events)
	{
		
		if(count($events)>0)	
		{
			for($p=0;$p<=count($events)-1;$p++)
			{
				//echo '<br>p is:'.$p;
				foreach($events[$p] as $key => $val) 
				{
				  $events[$p][$key] = $this->sanitize_val($val, $key);
				}			
			}
		}
		$this->events=$events;
		
	}

	function prepare()
	{
		$cp=array();
		if(count($this->events)>0)	
		{
		$cp[]= 'BEGIN:VCALENDAR';
		$cp[]= 'VERSION:2.0';
		$cp[]= 'PRODID:-//hacksw/handcal//NONSGML v1.0//EN';
		$cp[]= 'CALSCALE:GREGORIAN';
	
			for($p=0;$p<=count($this->events)-1;$p++)
			{
				$cp[]='BEGIN:VEVENT';
				foreach($this->events[$p] as $key => $val) 
				{
				  
				  $cp[]= strtoupper($key).':'.$val;
				 
				}
				$cp[]= 'RRULE:FREQ=YEARLY';
				$cp[]= 'END:VEVENT';			
			}
			$cp[]='END:VCALENDAR';
	
		}		
	
		return implode("\r\n", $cp);
	
	}
	
	private function sanitize_val($val, $key = false) 
	{
		switch($key) 
		{
	  		case 'dtend':
	        case 'dtstamp':
	  		case 'dtstart':
		        $val = $this->format_timestamp($val);
	    		break;
	      default:
		    $val = $this->escape_string($val);
	    }
		return $val;
	}
	private function format_timestamp($timestamp)
	{
		$dt = new DateTime($timestamp);
		return $dt->format(self::DT_FORMAT);
	}

	private function escape_string($str)
	{
		return preg_replace('/([,;])/','\$1', $str);
	}

}

$events = array();

$bdays = $_POST['bday'];

foreach($bdays as $k => $b){	
	$events[]=array(
		'description' => '🎉 '.$k,
		'summary' => '🎉 '.$k,
		'dtstart' => $b,
	);
}


$ics = new ics_events($events);

$file = 'generated/nogizaka46_'.uniqid().'.ics';
$current = file_get_contents($file);
$current .= $ics->prepare();
file_put_contents($file, $current);

unset($ics);

echo $file;
die();


?>
