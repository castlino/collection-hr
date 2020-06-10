<?php

echo "\n";

$csvFilePath = $argv[1];
if( !file_exists($csvFilePath) ){
  echo "File does not exits...\n";
  exit;
}


if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        for ($c=0; $c < $num; $c++) {
            echo $data[$c] . "<br />\n";
        }
    }
    fclose($handle);
}


while(true){
  echo "Input: ";
  $handle = fopen ("php://stdin","r");
  $line = fgets($handle);
  if( trim($line) == 'QUIT' ){
      echo "Ending script...\n";
      exit;
  }else{
      $inputs = explode(' ', $line);
      $from = strtoupper($inputs[0]);
      $to = strtoupper($inputs[1]);
      $latency = (int) $inputs[2];
  }
  echo "Output: ".$from."\n";
}
