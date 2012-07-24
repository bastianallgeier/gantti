<?php

class CalendarIterator implements Iterator {

  private $_ = array();

  public function __construct($array=array()) {
    $this->_ = $array;
  }
      
  function __toString() {
    $result = '';
    foreach($this->_ as $date) {
      $result .= $date . '<br />';
    }
    return $result;
  }      
      
  function rewind() {
    reset($this->_);
  }

  function current() {
    return current($this->_);
  }

  function key() {
    return key($this->_);
  }

  function next() {
    return next($this->_);
  }

  function prev() {
    return prev($this->_);
  }

  function valid() {
    $key = key($this->_);
    $var = ($key !== null && $key !== false);
    return $var;
  }
      
  function count() {
    return count($this->_);
  }  

  function first() {
    return array_shift($this->_);
  }

  function last() {
    return array_pop($this->_);
  }

  function nth($n) {
    $values = array_values($this->_);
    return isset($values[$n]) ? $values[$n] : null;  
  }

  function indexOf($needle) {
    return array_search($needle, array_values($this->_));
  }

  function toArray() {
    return $this->_;
  }

  function slice($offset=null, $limit=null) {
    if($offset === null && $limit === null) return $this;
    return new CalendarIterator(array_slice($this->_, $offset, $limit));
  }

  function limit($limit) {
    return $this->slice(0, $limit);
  }
    
}

class CalendarObj {
  
  var $yearINT;
  var $monthINT;
  var $dayINT;
  var $hourINT;
  var $minuteINT;
  var $secondINT;
  var $timestamp = 0;

  function __construct($year=false, $month=1, $day=1, $hour=0, $minute=0, $second=0) {

    if(!$year)  $year   = date('Y');
    if(!$month) $month  = date('m');
    if(!$day)   $day    = date('d');
    
    $this->yearINT   = intval($year);
    $this->monthINT  = intval($month);
    $this->dayINT    = intval($day);
    $this->hourINT   = intval($hour);
    $this->minuteINT = intval($minute);
    $this->secondINT = intval($second);
        
    // convert this to timestamp
    $this->timestamp = mktime($hour, $minute, $second, $month, $day, $year);
  }
  
  function year($year=false) {
    if(!$year) $year = $this->yearINT;
    return new CalendarYear($year, 1, 1, 0, 0, 0);
  }

  function month($month=false) {
    if(!$month) $month = $this->monthINT;
    return new CalendarMonth($this->yearINT, $month, 1, 0, 0, 0);
  }

  function day($day=false) {
    if(!$day) $day = $this->dayINT;
    return new CalendarDay($this->yearINT, $this->monthINT, $day, 0, 0, 0);
  }

  function hour($hour=false) {
    if(!$hour) $hour = $this->hourINT;
    return new CalendarHour($this->yearINT, $this->monthINT, $this->dayINT, $hour, 0, 0);
  }

  function minute($minute=false) {
    if(!$minute) $minute = $this->minuteINT;
    return new CalendarMinute($this->yearINT, $this->monthINT, $this->dayINT, $this->hourINT, $minute, 0);
  }

  function second($second=false) {
    if(!$second) $second = $this->secondINT;
    return new CalendarSecond($this->yearINT, $this->monthINT, $this->dayINT, $this->hourINT, $this->minuteINT, $second);
  }

  function timestamp() {
    return $this->timestamp;
  }

  function __toString() {
    return date('Y-m-d H:i:s', $this->timestamp);
  }
  
  function format($format) {
    return date($format, $this->timestamp);  
  }

  function iso() {
    return date(DATE_ISO, $this->timestamp);
  }

  function cookie() {
    return date(DATE_COOKIE, $this->timestamp);
  }

  function rss() {
    return date(DATE_RSS, $this->timestamp);
  }

  function atom() {
    return date(DATE_ATOM, $this->timestamp);
  }

  function mysql() {
    return date('Y-m-d H:i:s', $this->timestamp);
  }

  function time() {
    return strftime('%T', $this->timestamp);
  }

  function ampm() {
    return strftime('%p', $this->timestamp);
  }

  function modify($string) {
    $ts = (is_int($string)) ? $this->timestamp+$string : strtotime($string, $this->timestamp);
    
    list($year, $month, $day, $hour, $minute, $second) = explode('-', date('Y-m-d-H-i-s', $ts));
    return new CalendarDay($year, $month, $day, $hour, $minute, $second);
  }

  function plus($string) {
    $modifier = (is_int($string)) ? $string : '+' . $string;
    return $this->modify($modifier);                
  }

  function add($string) {
    return $this->plus($string);  
  }
  
  function minus($string) {
    $modifier = (is_int($string)) ? -$string : '-' . $string;
    return $this->modify($modifier);                  
  }

  function sub($string) {
    return $this->minus($string);  
  }
  
  function dmy() {
    return $this->format('d.m.Y');
  }

  function padded() {
    return str_pad($this->int(),2,'0',STR_PAD_LEFT);
  }

}

class Calendar {

  static $now = 0;

  function __construct() {
    Calendar::$now = time();
  }

  function years($start, $end) {
    $array = array();
    foreach(range($start, $end) as $year) {
      $array[] = $this->year($year);
    }
    return new CalendarIterator($array);
  }

  function year($year) {
    return new CalendarYear($year, 1, 1, 0, 0, 0);
  }

  function months($year=false) {
    $year = new CalendarYear($year, 1, 1, 0, 0, 0);
    return $year->months();    
  }

  function month($year, $month) {
    return new CalendarMonth($year, $month, 1, 0, 0);
  }
  
  function week($year=false, $week=false) {
    return new CalendarWeek($year, $week);  
  }

  function days($year=false) {
    $year = new CalendarYear($year);
    return $year->days();    
  }

  function day($year=false, $month=false, $day=false) {
    return new CalendarDay($year, $month, $day);    
  }
  
  function date() {

    $args = func_get_args();
    
    if(count($args) > 1) {

      $year   = isset($args[0]) ? $args[0] : false;
      $month  = isset($args[1]) ? $args[1] : 1;
      $day    = isset($args[2]) ? $args[2] : 1;
      $hour   = isset($args[3]) ? $args[3] : 0;
      $minute = isset($args[4]) ? $args[4] : 0;
      $second = isset($args[5]) ? $args[5] : 0;

    } else {
      
      if(isset($args[0])) {
        $ts = (is_int($args[0])) ? $args[0] : strtotime($args[0]);
      } else {
        $ts = time();
      }

      if(!$ts) return false;
      
      list($year, $month, $day, $hour, $minute, $second) = explode('-', date('Y-m-d-H-i-s', $ts));
      
  	} 

    return new CalendarDay($year, $month, $day, $hour, $minute, $second);

  }
  
  function today() {
    return $this->date('today');
  }

  function now() {
    return $this->today();
  }

  function tomorrow() {
    return $this->date('tomorrow');
  }

  function yesterday() {
    return $this->date('yesterday');
  }
  
}

class CalendarYear extends CalendarObj {

  function __toString() {
    return $this->format('Y');
  }

  function int() {
    return $this->yearINT;
  }

  function months() {
    $array = array();
    foreach(range(1, 12) as $month) {
      $array[] = $this->month($month);
    }
    return new CalendarIterator($array);
  }
  
  function month($month=1) {
    return new CalendarMonth($this->yearINT, $month);
  }

  function weeks() {
    $array = array();
    $weeks = (int)date('W', mktime(0,0,0,12,31,$this->int))+1;
    foreach(range(1,$weeks) as $week) {
      $array[] = new CalendarWeek($this, $week);
    }
    return new CalendarIterator($array);
  }
  
  function week($week=1) {
    return new CalendarWeek($this, $week);  
  }
  
  function countDays() {
    return (int)date('z', mktime(0,0,0,12,31,$this->yearINT))+1;  
  }
    
  function days() {
        
    $days  = $this->countDays();
    $array = array();
    $ts    = false;

    for($x=0; $x<$days; $x++) {
      $ts      = (!$ts) ? $this->timestamp : strtotime('tomorrow', $ts);
      $month   = date('m', $ts);      
      $day     = date('d', $ts);
      $array[] = $this->month($month)->day($day);
    }

    return new CalendarIterator($array);      

  }
    
  function next() {
    return $this->plus('1year')->year();
  }
  
  function prev() {
    return $this->minus('1year')->year();
  }
  
  function name() {
    return $this->int();
  }
  
  function firstMonday() {
    $cal = new Calendar();
    return $cal->date(strtotime('first monday of ' . date('Y', $this->timestamp)));   
  }

  function firstSunday() {
    $cal = new Calendar();
    return $cal->date(strtotime('first sunday of ' . date('Y', $this->timestamp)));   
  }
  
}

class CalendarMonth extends CalendarObj {

  function __toString() {
    return $this->format('Y-m');
  }

  function int() {
    return $this->monthINT;
  }

  function weeks($force=false) {

    $first = $this->firstDay();
    $week  = $first->week();    
        
    $currentMonth = $this->int();
    $nextMonth    = $this->next()->int();

    $max = ($force) ? $force : 6;
      
    for($x=0; $x<$max; $x++) {
      
      // make sure not to add weeks without a single day in the same month
      if(!$force && $x>0 && $week->firstDay()->month()->int() != $currentMonth) break;
      
      $array[] = $week;
            
      // make sure not to add weeks without a single day in the same month
      if(!$force && $week->lastDay()->month()->int() != $currentMonth) break;

      $week = $week->next();

    }
        
    return new CalendarIterator($array);
        
  }

  function countDays() {
    return date('t', $this->timestamp);  
  }

  function firstDay() {
    return new CalendarDay($this->yearINT, $this->monthINT, 1);
  }

  function lastDay() {
    return new CalendarDay($this->yearINT, $this->monthINT, $this->countDays());  
  }

  function days() {
    
    // number of days per month
    $days  = date('t', $this->timestamp);
    $array = array();
    $ts    = $this->firstDay()->timestamp();

    foreach(range(1, $days) as $day) {
      $array[] = $this->day($day);    
    }

    return new CalendarIterator($array);      

  }

  function day($day=1) {
    return new CalendarDay($this->yearINT, $this->monthINT, $day);
  }
  
  function next() {
    return $this->plus('1month')->month();
  }
  
  function prev() {
    return $this->minus('1month')->month();
  }
  
  function name() {
    return strftime('%B', $this->timestamp);
  }

  function shortname() {
    return strftime('%b', $this->timestamp);
  }

}

class CalendarWeek extends CalendarObj {

  function __toString() {
    return $this->firstDay()->format('Y-m-d') . ' - ' . $this->lastDay()->format('Y-m-d');
  }

  var $weekINT;

  function int() {
    return $this->weekINT;
  }

  function __construct($year=false, $week=false) {

    if(!$year) $year = date('Y');
    if(!$week) $week = date('W');

    $this->yearINT = intval($year);
    $this->weekINT = intval($week);

    $ts     = strtotime('Thursday', strtotime($year . 'W' . $this->padded()));
    $monday = strtotime('-3days', $ts);

    parent::__construct(date('Y', $monday), date('m', $monday), date('d', $monday), 0, 0, 0);
    
  }
  
  function years() {
    $array = array();
    $array[] = $this->firstDay()->year();
    $array[] = $this->lastDay()->year();
      
    // remove duplicates
    $array = array_unique($array);
    
    return new CalendarIterator($array);
  }

  function months() {
    $array = array();
    $array[] = $this->firstDay()->month();
    $array[] = $this->lastDay()->month();

    // remove duplicates
    $array = array_unique($array);

    return new CalendarIterator($array);
  }
  
  function firstDay() {
    $cal = new Calendar();
    return $cal->date($this->timestamp);    
  }

  function lastDay() {
    $first = $this->firstDay();
    return $first->plus('6 days');
  }
    
  function days() {
    
    $day   = $this->firstDay();
    $array = array();
                
    for($x=0; $x<7; $x++) {
      $array[] = $day;
      $day = $day->next();
    }
        
    return new CalendarIterator($array);

  }

  function next() {

    $next = strtotime('Thursday next week', $this->timestamp);
    $year = date('Y', $next);
    $week = date('W', $next);
                                  
    return new CalendarWeek($year, $week);

  }
  
  function prev() {

    $prev = strtotime('Monday last week', $this->timestamp);
    $year = date('Y', $prev);
    $week = date('W', $prev);

    return new CalendarWeek($year, $week);

  }
   
}  
  

class CalendarDay extends CalendarObj {

  function __toString() {
    return $this->format('Y-m-d');
  }

  function int() {
    return $this->dayINT;
  }
  
  function week() {
    $week = date('W', $this->timestamp);
    $year = ($this->monthINT == 1 && $week > 5) ? $this->year()->prev() : $this->year();
    return new CalendarWeek($year->int(), $week);      
  }

  function next() {
    return $this->plus('1day');
  }
  
  function prev() {
    return $this->minus('1day');
  }

  function weekday() {
    return date('N', $this->timestamp);
  }
  
  function name() {
    return strftime('%A', $this->timestamp);
  }

  function shortname() {
    return strftime('%a', $this->timestamp);
  }
    
  function isToday() {
    $cal = new Calendar();
    return $this == $cal->today();
  }
  
  function isYesterday() {
    $cal = new Calendar();
    return $this == $cal->yesterday();  
  }
  
  function isTomorrow() {
    $cal = new Calendar();
    return $this == $cal->tomorrow();    
  }
  
  function isInThePast() {
    return ($this->timestamp < Calendar::$now) ? true : false;
  }
  
  function isInTheFuture() {
    return ($this->timestamp > Calendar::$now) ? true : false;  
  }

  function isWeekend() {
    $num = $this->format('w');
    return ($num == 6 || $num == 0) ? true : false;
  }

  function hours() {

    $obj   = $this;
    $array = array();
    
    while($obj->int() == $this->int()) {
      $array[] = $obj->hour();
      $obj = $obj->plus('1hour');    
    }
    
    return new CalendarIterator($array);

  }

}

class CalendarHour extends CalendarObj {

  function int() {
    return $this->hourINT;
  }

  function minutes() {

    $obj   = $this;
    $array = array();

    while($obj->hourINT == $this->hourINT) {
      $array[] = $obj;
      $obj = $obj->plus('1minute')->minute();    
    }

    return new CalendarIterator($array);
        
  }
  
  function next() {
    return $this->plus('1hour')->hour();
  }

  function prev() {
    return $this->minus('1hour')->hour();
  }

}

class CalendarMinute extends CalendarObj {

  function int() {
    return $this->minuteINT;
  }

  function seconds() {

    $obj   = $this;
    $array = array();
    
    while($obj->minuteINT == $this->minuteINT) {
      $array[] = $obj;
      $obj = $obj->plus('1second')->second();    
    }
    
    return new CalendarIterator($array);

  }

  function next() {
    return $this->plus('1minute')->minute();
  }

  function prev() {
    return $this->minus('1minute')->minute();
  }

}

class CalendarSecond extends CalendarObj {

  function int() {
    return $this->secondINT;
  }
  
  function next() {
    return $this->plus('1second')->second();
  }

  function prev() {
    return $this->minus('1second')->second();
  }

}



