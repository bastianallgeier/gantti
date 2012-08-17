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
  var $months    = array();
  var $days      = array();
  var $seconds   = 0;

  function __construct($data, $params=array()) {
    
    $defaults = array(
      'title'      => false,
      'cellwidth'  => 40,
      'cellheight' => 40,
      'today'      => true,
    );
        
    $this->options = array_merge($defaults, $params);    
    $this->cal     = new Calendar();
    $this->data    = $data;
    $this->seconds = 60*60*24;

    $this->cellstyle = 'style="width: ' . $this->options['cellwidth'] . 'px; height: ' . $this->options['cellheight'] . 'px"';
    
    // parse data and find first and last date  
    $this->parse();                
                    
  }

  function parse() {
    
    foreach($this->data as $d) {
              
      $this->blocks[] = array(
        'label' => $d['label'],
        'start' => $start = strtotime($d['start']),
        'end'   => $end   = strtotime($d['end']),
        'class' => @$d['class']
      );
      
      if(!$this->first || $this->first > $start) $this->first = $start;
      if(!$this->last  || $this->last  < $end)   $this->last  = $end;
          
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
    $html[] = '<aside>';
    $html[] = '<ul class="gantt-labels" style="margin-top: ' . (($this->options['cellheight']*2)+1) . 'px">';
    foreach($this->blocks as $i => $block) {
      $html[] = '<li class="gantt-label"><strong ' . $cellstyle . '>' . $block['label'] . '</strong></li>';      
    }
    $html[] = '</ul>';
    $html[] = '</aside>';

    // data section
    $html[] = '<section class="gantt-data">';
        
    // data header section
    $html[] = '<header>';

    // months headers
    $html[] = '<ul class="gantt-months" ' . $totalstyle . '>';
    foreach($this->months as $month) {
      $html[] = '<li class="gantt-month" style="width: ' . ($this->options['cellwidth'] * $month->countDays()) . 'px"><strong ' . $cellstyle . '>' . $month->name() . '</strong></li>';
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

        $html[] = '<li class="gantt-day' . $weekend . $today . '" ' . $wrapstyle . '><span ' . $cellstyle . '>' . $day . '</span></li>';
      }                      
      $html[] = '</ul>';    

      // the block
      $days   = (($block['end'] - $block['start']) / $this->seconds);
      $offset = (($block['start'] - $this->first->month()->timestamp) / $this->seconds);
      $top    = round($i * ($this->options['cellheight'] + 1));
      $left   = round($offset * $this->options['cellwidth']);
      $width  = round($days * $this->options['cellwidth'] - 9);
      $height = round($this->options['cellheight']-8);
      $class  = ($block['class']) ? ' ' . $block['class'] : '';
      $html[] = '<span class="gantt-block' . $class . '" style="left: ' . $left . 'px; width: ' . $width . 'px; height: ' . $height . 'px"><strong class="gantt-block-label">' . $days . '</strong></span>';
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
  
  function __toString() {
    return $this->render();
  }

}
