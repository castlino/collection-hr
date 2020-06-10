<?php

class Collection {
  private $items = [];
  
  private $returnPath = "";
  private $iOrigin = "";
  private $iLatency = "";
  private $rTotal = 0;
  private $rIndexes = [];
  
  public function add($obj){
    array_push($this->items, $obj);
  }
  
  public function count(){
    return count($this->items);
  }
  
  public function getPath($from, $to, $latency, $path="", $idxCont = []){
    static $stopFlag = false;
    
    // Initialize variables.
    $this->returnPath = $path;
    if ( $path=="" ) {
      $this->returnPath = $from;
      $this->iOrigin = $from;
      $this->rTotal = 0;
      $this->iLatency = $latency;
      $this->rIndexes = [];
      $stopFlag = false;
    }
    
    if ( !$stopFlag ) {
      $this->rIndexes = $idxCont;
      $indexes = $this->getFromItems($from);
      foreach($indexes as $ctrNdx => $ndx){
          if( !$stopFlag ) {
            
            // Set the next item to search
            if($ndx['link'] == 'from' ){
              $from = $this->items[$ndx['key']]['to'];
            }else{
              $from = $this->items[$ndx['key']]['from'];
            }
            
            // Save item
            array_push($this->rIndexes, $ndx['key']);
            
            // Assemble path and get total latency.
            $this->returnPath = $this->iOrigin;
            $currDev = $this->iOrigin;
            $this->rTotal = 0;
            foreach( $this->rIndexes as $rIdx ) {
              if ( $currDev!=$this->items[$rIdx]['to'] ) {
                $this->returnPath .= " => " . $this->items[$rIdx]['to'];
                $currDev = $this->items[$rIdx]['to'];
              } else {
                $this->returnPath .= " => " . $this->items[$rIdx]['from'];
                $currDev = $this->items[$rIdx]['from'];
              }
              $this->rTotal += $this->items[$rIdx]['latency'];
            }
          
            // Check if conditions met.
            if ( $from == $to ) {
              if( $this->rTotal <= $this->iLatency ) {
                $stopFlag = true;
                return $this->returnPath;
              } else {
                $this->rIndexes = $idxCont;
                array_pop($this->rIndexes);
                $this->returnPath = $path;
                $this->rTotal = 0;
              }
              break;
            } else {
                $this->getPath($from, $to, $latency, $this->returnPath, $this->rIndexes);
            }
          }
      }
      if( !$stopFlag ){
        $this->rIndexes = $idxCont;
        array_pop($this->rIndexes);
        $this->returnPath = $path;
        $this->rTotal = 0;
      }
    }
    
    
    if( !$stopFlag ){
        return "Path not found";
    }else{
        return $this->returnPath . " => " . $this->rTotal;
    }
  }
  
  private function getFromItems($from){
    $keys = [];
    foreach($this->items as $key=>$item){
      if( ( $item['to'] == $from || $item['from'] == $from ) && !in_array($key, $this->rIndexes) ){
        // Check if item found is from 'to' or 'from' key.
        if($item['to'] == $from ){
          array_push($keys, ['key'=>$key, 'link'=>'to']);
        }else{
          array_push($keys, ['key'=>$key, 'link'=>'from']);
        }
      }
    }
    return $keys;
  }
  
}