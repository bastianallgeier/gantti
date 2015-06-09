<?php

require('calendar.php'); 

class Gantti {

  var $cal       = null;
  var $data      = array();
  var $first     = false;
  var $last      = false;
  var $options   = array();
  var $cellstyle = false;
  var $blocks    = array();
  var $aside    = array();
  var $months    = array();
  var $days      = array();
  var $seconds   = 0;
  var $aside_width = '0px';

  function __construct($data, $params=array()) {
    
    $defaults = array(
      'title'      => false,
      'cellwidth'  => 40,
      'cellheight' => 40,
      'today'      => true,
      'aside'	   => true,		
    );
        
    $this->options = array_merge($defaults, $params);
    $this->first   = isset($this->options['first']) ? strtotime($this->options['first']) : false;
    $this->cal     = new Calendar();
    $this->data    = $data;
    $this->seconds = 60*60*24;

    $this->cellstyle = 'style="width: ' . $this->options['cellwidth'] . 'px; height: ' . $this->options['cellheight'] . 'px"';
    
    // parse data and find first and last date  
    $this->parse();                
                    
  }

  function parse() {
    if(!empty($this->data)){
	    foreach($this->data as $d) {
		  $tmp = array('label'   => $d['label'], 'content' => array(),'mark' => array());	        
		  foreach($d['content'] as $c){
			array_push($tmp['content'],array(
					'start' => $start = strtotime($c['start']),
					'end'   => $end   = strtotime($c['end']),
					'class' => @$c['class'],
					'text'  => @$c['text'],
				)
			);	
			if(!$this->first || $this->first > $start) $this->first = $start;
			if(!$this->last  || $this->last  < $end)   $this->last  = $end;	
		 }
		 
		 if(isset($d['mark']) && !empty($d['mark'])){
		 	foreach($d['mark'] as $m){
		 		array_push($tmp['mark'],array(
			 		'date' => $m['date'],
			 		'param'   => @$m['param'],
			 		'class' => @$m['class'],
			 		)
		 		);
		 	}
		 }
	      array_push($this->blocks,$tmp);
	      /* kumpulkan label, jika sama jadikan satu saja */
	      if(!isset($this->aside[$d['label']])){
	      	$this->aside[$d['label']]['label'] = $d['label'];
	      	$this->aside[$d['label']]['count'] = 0;
	      }
	      $this->aside[$d['label']]['count']++;
	    }
	    
	    $this->first = $this->cal->date($this->first);
	    $this->last  = $this->cal->date($this->last);
	
	    $current = $this->first->month();
	    $lastDay = $this->last->month()->lastDay()->timestamp;
	
	    // build the months      
	    while($current->lastDay()->timestamp <= $lastDay) {
	      $month = $current->month();
	      $this->months[] = $month;
	      foreach($month->days() as $day) {
	        $this->days[] = $day;
	      }
	      $current = $current->next();
	    }
     }        
  }

  function render() {
    
    $html = array();
    
    // common styles    
    $cellstyle  = 'style="line-height: ' . $this->options['cellheight'] . 'px; height: ' . $this->options['cellheight'] . 'px"';
    $wrapstyle  = 'style="width: ' . $this->options['cellwidth'] . 'px"';
    $totalstyle = 'style="width: ' . (count($this->days)*$this->options['cellwidth']) . 'px"';
    // start the diagram    
    $html[] = '<figure class="gantt">';    

    // set a title if available
    if($this->options['title']) {
      $html[] = '<figcaption>' . $this->options['title'] . '</figcaption>';
    }

    // sidebar with labels
    if($this->options['aside']){  
    	$this->aside_width = '200px';
	    $html[] = '<aside>';
	    $html[] = '<ul class="gantt-labels" style="margin-top: ' . (($this->options['cellheight']*2)+1) . 'px">';
	    foreach($this->aside as $i => $aside) {
	      $html[] = '<li class="gantt-label '.$aside['label'].'"><strong ' . $this->setAsideCellStyle($aside['count']) . '>' . $aside['label'] . '</strong></li>';      
	    }
	    $html[] = '</ul>';
	    $html[] = '</aside>';
    }
    // data section
    $html[] = '<section class="gantt-data" style="margin-left:'.$this->aside_width.'">';
        
    // data header section
    $html[] = '<header>';

    // months headers
    $html[] = '<ul class="gantt-months" ' . $totalstyle . '>';
    foreach($this->months as $month) {
      $html[] = '<li class="gantt-month" style="width: ' . ($this->options['cellwidth'] * $month->countDays()) . 'px"><strong ' . $cellstyle . '>' . $month->name() .' '.$month->year(). '</strong></li>';
    }                      
    $html[] = '</ul>';    

    // days headers
    $html[] = '<ul class="gantt-days" ' . $totalstyle . '>';
    foreach($this->days as $day) {

      $weekend = ($day->isWeekend()) ? ' weekend' : '';
      $today   = ($day->isToday())   ? ' today' : '';

      $html[] = '<li class="gantt-day' . $weekend . $today . '" ' . $wrapstyle . '><span ' . $cellstyle . '>' . $day->padded() . '</span></li>';
    }                      
    $html[] = '</ul>';    
    
    // end header
    $html[] = '</header>';

    // main items
    $html[] = '<ul class="gantt-items" ' . $totalstyle . '>';
        
    foreach($this->blocks as $i => $block) {
      
      $html[] = '<li class="gantt-item">';
      
      // days
      $html[] = '<ul class="gantt-days">';
      foreach($this->days as $day) {
      	
        $weekend = ($day->isWeekend()) ? ' weekend' : '';
        $today   = ($day->isToday())   ? ' today' : '';
		/* tandai hari2 tertentu */
        
        $mark_class = array();
        if(isset($block['mark']) && !empty($block['mark'])){
        	foreach($block['mark'] as $mr){
        		if($mr['param'] == '<'){
        			if($mr['date'] < $day){
        				array_push($mark_class,$mr['class']);	
        			}
        		}
        		if($mr['param'] == '>'){
        			if($mr['date'] > $day){
        				array_push($mark_class,$mr['class']);
        			}
        		}
        		if($mr['param'] == '='){
        			
        			if($mr['date'] == $day){
        				array_push($mark_class,$mr['class']);
        			}
        		}
        		 
        	}	
        }
        
        $html[] = '<li class="gantt-day' . $weekend . $today .' ' .implode(' ',$mark_class).'" ' . $wrapstyle . '><span ' . $cellstyle . '></span></li>';
      }                      
      $html[] = '</ul>';    

      // the block 
      foreach($block['content'] as $b){
		  $days   = (($b['end'] - $b['start']) / $this->seconds) + 1;
		  $offset = (($b['start'] - $this->first->month()->timestamp) / $this->seconds);
		  $top    = round($i * ($this->options['cellheight'] + 1));
		  $left   = round($offset * $this->options['cellwidth']);
		  $width  = round($days * $this->options['cellwidth'] - 9);
		  $height = round($this->options['cellheight']-8);
		  $class  = ($b['class']) ? ' ' . $b['class'] : '';
		  $text = isset($b['text']) ? $b['text'] : $days; 
		  $html[] = '<span class="gantt-block' . $class . '" style="left: ' . $left . 'px; width: ' . $width . 'px; height: ' . $height . 'px"><strong class="gantt-block-label">' . $text . '</strong></span>';	
	  }
      $html[] = '</li>';
    
    }
    
    $html[] = '</ul>';    
    
    if($this->options['today']) {
    
      // today
      $today  = $this->cal->today();
      $offset = (($today->timestamp - $this->first->month()->timestamp) / $this->seconds); 
      $left   = round($offset * $this->options['cellwidth']) + round(($this->options['cellwidth'] / 2) - 1);
          
      if($today->timestamp > $this->first->month()->firstDay()->timestamp && $today->timestamp < $this->last->month()->lastDay()->timestamp) {
        $html[] = '<time style="top: ' . ($this->options['cellheight'] * 2) . 'px; left: ' . $left . 'px" datetime="' . $today->format('Y-m-d') . '">Today</time>';
      }

    }
    
    // end data section
    $html[] = '</section>';    

    // end diagram
    $html[] = '</figure>';

    return implode('', $html);
      
  }

  function setAsideCellStyle($count = 1){
  	return 'style="line-height: ' . $this->options['cellheight']*$count . 'px; height: ' . $this->options['cellheight']*$count . 'px"';
  }
  

  function __toString() {
    return $this->render();
  }

}
