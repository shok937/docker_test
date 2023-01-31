<?php

date_default_timezone_set('Asia/Yekaterinburg');

/*

дата начала, дата окончания, название, описание, место, ссылка на сайт с информацией о мероприятии.

/ics.php?START=$arr['dtstart']&END=$arr['dtstart']&TITLE=$arr['title']&DESCRIPTION=$arr['desc']&LOC=$arr['place']&URL=$arr['url']

*/

class ICS {
	const DT_FORMAT = 'Ymd\THis\Z';
	protected $properties = array();
	private $available_properties = array(
		'description',
		'dtend',
		'dtstart',
		'location',
		'summary',
		'url',
		'SUMMARY;LANGUAGE=ru'
	);

	public function __construct($props) {
		$this->set($props);
	}

	public function set($key, $val = false) 
	{
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->set($k, $v);
			}
		} else {
			if (in_array($key, $this->available_properties)) {
				$this->properties[$key] = $this->sanitize_val($val, $key);
			}
		}
	}

	public function to_string() {
		$rows = $this->build_props();
		return implode("\r\n", $rows);
	}
	private function build_props() {
		// Build ICS properties - add header
		$ics_props = array(
			'BEGIN:VCALENDAR',
			'VERSION:2.0',
			'X-WR-CALNAME:'.$_GET['TITLE'],
			'PRODID:-//hacksw/handcal//NONSGML v1.0//EN',
			'CALSCALE:GREGORIAN',
			'BEGIN:VEVENT'
		);
		// Build ICS properties - add header
		$props = array();
		foreach($this->properties as $k => $v) {
			$props[strtoupper($k . ($k === 'url' ? ';VALUE=URI' : ''))] = $v;
		}
		// Set some default values
		$props['DTSTAMP'] = $this->format_timestamp('now');
		$props['UID'] = uniqid();
		// Append properties
		foreach ($props as $k => $v) {
			$ics_props[] = "$k:$v";
		}
		// Build ICS properties - add footer
		$ics_props[] = 'END:VEVENT';
		$ics_props[] = 'END:VCALENDAR';
		return $ics_props;
	}

	private function sanitize_val($val, $key = false) {
		switch($key) {
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

	private function format_timestamp($timestamp) {
		$dt = new DateTime($timestamp);
		 $dt->modify("-5 hours"); // +05:00 -> +00:00
		// var_dump($dt);
		return $dt->format(self::DT_FORMAT);
		// return date('Ymd\THis\Z', strtotime($timestamp));
	}

	private function escape_string($str) {
		return preg_replace('/([\,;])/','\\\$1', $str);
	}
}

if (isset($_GET['ics_start'], $_GET['ics_end'], $_GET['ics_loc'], $_GET['ics_desc'], $_GET['ics_url'], $_GET['ics_title']))
{
	header('Content-Type: text/calendar; charset=utf-8');
	header('Content-Disposition: attachment; filename=invite'.time().'.ics');

	$ics = new ICS(array(
		'dtstart' => $_GET['ics_start'], //'2020-09-15 16:00', //$_GET['START']
		'dtend' => $_GET['ics_end'], //'2020-09-15 17:30', // $_GET['END']
		'location' => $_GET['ics_loc'], //'Автоград, Республики 280', //$_GET['LOC']
		'description' => $_GET['ics_desc'], //'Вы записались на сервис', //$_GET['DESCRIPTION']
		'url' => $_GET['ics_url'], //'https://lk.agrad.ru/', //$_GET['URL']
		'SUMMARY;LANGUAGE=ru' => $_GET['ics_title'], //'Запись на сервис', //$_GET['TITLE']
	));

	echo $ics->to_string();
}
else
{
	header('HTTP/1.1 404 Not Found');
	echo '<h1>404</h1><br />not found';
	exit();
}


?>