<?php

class Collection{
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
    
    $this->returnPath = $path;
    if($path==""){
      echo "initializing...";
      $this->returnPath = $from;
      $this->iOrigin = $from;
      $this->rTotal = 0;
      $this->iLatency = $latency;
      $this->rIndexes = [];
      $stopFlag = false;
    }
    
    
    if( !$stopFlag ){
      $this->rIndexes = $idxCont;
      $indexes = $this->getFromItems($from);
      $tmpPath = $path;
      foreach($indexes as $ctrNdx => $ndx){
          if( !$stopFlag ){
            echo "\nbefore setting from:";
            echo "\nfrom: ". $from;
            echo "\n";
            echo json_encode($this->items[$ndx['key']]);
            echo "\n";
            echo json_encode(end($indexes));
            if($ndx['link'] == 'from' ){
              $from = $this->items[$ndx['key']]['to'];
            }else{
              $from = $this->items[$ndx['key']]['from'];
            }
            $this->rTotal += $this->items[$ndx['key']]['latency'];
            array_push($this->rIndexes, $ndx['key']);
            
            echo "\n..". json_encode($this->rIndexes);
            echo "\n". $this->returnPath . "(".$from.", ". $to."), latency: " . $this->rTotal. "\n";
            echo "\nCtrNdx: ".$ctrNdx;
            echo "\nfrom: ".$from;
            echo "\nto: ".$to;
          
            if($from == $to){
              echo "\nexiting...";
              if($this->rTotal <= $this->iLatency){
                $stopFlag = true;
                echo "\n stopping... ".$this->rTotal;
                return $this->returnPath;
              }else{
                $this->rIndexes = $idxCont;
                array_pop($this->rIndexes);
                $this->returnPath = $path;
                $this->rTotal = 0;
              }
              break;
            }else{
                
                echo "\nassembling path..";
                $this->returnPath = $this->iOrigin;
                $currDev = $this->iOrigin;
                $this->rTotal = 0;
                foreach($this->rIndexes as $rIdx){
                  if($currDev!=$this->items[$rIdx]['to']){
                    $this->returnPath .= " => " . $this->items[$rIdx]['to'];
                    $currDev = $this->items[$rIdx]['to'];
                  }else{
                    $this->returnPath .= " => " . $this->items[$rIdx]['from'];
                    $currDev = $this->items[$rIdx]['from'];
                  }
                  echo "\n --latency: ".$this->items[$rIdx]['latency'];
                  $this->rTotal += $this->items[$rIdx]['latency'];
                }
                echo "\n". $this->returnPath . "(".$from.", ". $to."), latency: " . $this->rTotal. "\n";
                
                $this->getPath($from, $to, $latency, $this->returnPath, $this->rIndexes);
            }
          }
      }
      echo "\nnext loop in recursion...";
      if( !$stopFlag ){
        $this->rIndexes = $idxCont;
        array_pop($this->rIndexes);
        $this->returnPath = $path;
        $this->rTotal = 0;
      }
    }
    
    
    if( !$stopFlag ){
      return "Path not found";
    }
    echo (json_encode($this->rIndexes));
    return $this->returnPath . " => " . $to . " => " . $this->rTotal;
  }
  
  private function getFromItems($from){
    $keys = [];
    foreach($this->items as $key=>$item){
      if( ( $item['to'] == $from || $item['from'] == $from ) && !in_array($key, $this->rIndexes) ){
        if($item['to'] == $from ){
          array_push($keys, ['key'=>$key, 'link'=>'to']);
        }else{
          array_push($keys, ['key'=>$key, 'link'=>'from']);
        }
      }
    }
    echo "\n@ START getFromItems...\n";
    echo ">".json_encode($this->rIndexes)."<";
    echo ">".json_encode($keys)."<";
    echo "\n@ END getFromItems...\n";
    return $keys;
  }
  
}