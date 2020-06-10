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
        $num = count($data);
        // for ($c=0; $c < $num; $c++) {
        //     echo $data[$c] . "<br />\n";
        // }
    }
    fclose($handle);
}

// echo "\ncount total: ". $itemsCollection->count();
// echo "\n final path: ";
// // $finalPath = $itemsCollection->getPath('A', 'F', 1120);
// // $finalPath = $itemsCollection->getPath('A', 'F', 1090);
// // $finalPath = $itemsCollection->getPath('A', 'D', 100);
// // $finalPath = $itemsCollection->getPath('E', 'A', 400);
// // $finalPath = $itemsCollection->getPath('E', 'A', 80);
// echo $finalPath;
// echo "\n";

while(true){
  echo "Input: ";
  $handle = fopen ("php://stdin","r");
  $line = fgets($handle);
  if( trim($line) == 'QUIT' || trim($line) == 'quit' ){
      echo "Ending script...\n";
      exit;
  }else{
      $inputs = explode(' ', $line);
      $from = strtoupper($inputs[0]);
      $to = strtoupper($inputs[1]);
      $latency = (int) $inputs[2];
      $finalPath = $itemsCollection->getPath($from, $to, $latency);
  }
  echo "Output: ".$finalPath."\n";
}
