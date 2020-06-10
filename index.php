<?php
require_once "Collection.php";

echo "\n";

$csvFilePath = $argv[1];
if( !file_exists($csvFilePath) ){
  echo "File does not exits...\n";
  exit;
}

$itemsCollection = new Collection();
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $itemsCollection->add(['from'=>$data[0], 'to'=>$data[1], 'latency'=>$data[2]]);
    }
    fclose($handle);
}

while(true){
  echo "Input: ";
  $handle = fopen ("php://stdin","r");
  $line = fgets($handle);
  if( trim($line) == 'QUIT' || trim($line) == 'quit' ){
      echo "Ending script...\n";
      exit;
  }else{
      $inputs = explode(' ', $line);
      if( count($inputs) != 3){
        echo "Output: Input error... Syntax: \"[from] [to] [latency]\", type \"QUIT\" to terminate script.\n";
        continue;
      }
      $from = strtoupper(trim($inputs[0]));
      $to = strtoupper(trim($inputs[1]));
      $latency = (int) $inputs[2];
      $finalPath = $itemsCollection->getPath($from, $to, $latency);
  }
  echo "Output: ".$finalPath."\n";
}
