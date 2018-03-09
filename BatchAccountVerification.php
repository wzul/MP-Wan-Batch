<?php

require 'vendor/autoload.php';

use Billplz\API;
use Billplz\Connect;

ini_set('display_errors', 'On');
error_reporting(E_ALL);
set_time_limit(0);

echo 'Make sure you set to UTF-8 character encoding on the file! '.PHP_EOL;
echo 'The expected arrangement is: Bank Code, Bank Account Number, ID Number, Bank Account Holder Name, Total, Description, Email, ID, Status' . PHP_EOL;
echo PHP_EOL;

$filename = $argv[1];
$api_key = $argv[2];

//$filename = 'mp-alegion.csv';
//$api_key = '4e49de80-1670-4606-84f8-2f1d33a38670';

echo 'Reading file contents...'.PHP_EOL.PHP_EOL;

$filecsv = file_get_contents($filename);
$array = array_map("str_getcsv", explode("\n", $filecsv));
$bank_account = array();

$index_1 = 0;
$index_2 = 0;

foreach ($array as $data) {
    if ($data[0] === 'Bank Code') {
        continue;
    }

    if (empty($data[0])) {
        break;
    }

    $bank_account[] = array(
        'name'=>$data[3],
        'id_no'=>$data[2],
        'acc_no'=>$data[1],
        'code'=>$data[0],
        'organization'=>'false'
    );
}

$connnect = (new Connect($api_key))->detectMode();
$billplz = new API($connnect);

$response = array();

echo 'Sending to Billplz API...'. PHP_EOL.PHP_EOL;

foreach ($bank_account as $bank) {
    $response[] = $billplz->toArray($billplz->createBankAccount($bank));
}

echo 'Writing to csv file...'. PHP_EOL.PHP_EOL;

for ($i = 0; $i<sizeof($response); $i++) {
    if ($response[$i][0] === 200) {
        $array[$i+1][8] = $response[$i][1]['status'];
    }
}

$fp = fopen($filename, 'w');
foreach ($array as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
