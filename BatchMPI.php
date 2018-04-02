<?php

require 'vendor/autoload.php';

use Billplz\API;
use Billplz\Connect;

ini_set('display_errors', 'On');
error_reporting(E_ALL);
set_time_limit(0);

echo 'Make sure you set to UTF-8 character encoding on the file! '.PHP_EOL;
echo 'The expected arrangement is: Bank Code, Bank Account Number, ID Number, Bank Account Holder Name, Total, Description, Email, ID, Status, MP Collection ID' . PHP_EOL;
echo PHP_EOL;

$filename = $argv[1];
$api_key = $argv[2];

//$filename = 'terbaru.csv';
//$api_key = '4e49de80-1670-4606-84f8-2f1d33a38670';

echo 'Reading file contents...'.PHP_EOL.PHP_EOL;

$filecsv = @file_get_contents($filename);

if (!$filecsv) {
    exit('Failed to read file contents');
}

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

    $parameter = array(
        'mass_payment_instruction_collection_id' => $data[9],
        'bank_code'=> $data[0],
        'bank_account_number' => $data[1],
        'identity_number'=>$data[2],
        'name' => $data[3],
        'description' => $data[5],
        'total' => preg_replace("/[^0-9.]/","",$data[4]) * 100
    );
    $optional = array(
        'email' => $data[6]
    );

    $bank_account[] = array(
        'parameter' => $parameter,
        'optional' => $optional,
        'id' => $data[7]
    );
}


$connnect = (new Connect($api_key))->detectMode();
$billplz = new API($connnect);

$response = array();

echo 'Sending to Billplz API...'. PHP_EOL.PHP_EOL;

foreach ($bank_account as $bank) {
    if (empty($bank['id'])) {
        $response[] = $billplz->toArray($billplz->createMPI($bank['parameter'], $bank['optional']));
    } else {
        $response[] = array(200,array('id'=> $bank['id'],'status' => 'duplicate'));
    }
}

echo 'Writing to csv file...'. PHP_EOL.PHP_EOL;

for ($i = 0; $i<sizeof($response); $i++) {
    if ($response[$i][0] === 200) {
        $array[$i+1][7] = $response[$i][1]['id'];
        $array[$i+1][8] = $response[$i][1]['status'];
    } else {
        $array[$i+1][8] = implode(',', $response[$i][1]['error']['message']);
    }
}

echo 'Generating Success Files...'.PHP_EOL;

$fp = fopen('success_'. $filename, 'w');
foreach ($array as $fields) {
    if (!empty($fields[7])) {
        fputcsv($fp, $fields);
    }
}
fclose($fp);
echo 'Success...'.PHP_EOL.PHP_EOL;

echo 'Generating Failed Files...'.PHP_EOL;

$printheader = true;
$fp = fopen('failed_'. $filename, 'w');
foreach ($array as $fields) {
    if (empty($fields[7]) || $printheader) {
        fputcsv($fp, $fields);
        $printheader = false;
    }
}
fclose($fp);

echo 'Success...'.PHP_EOL.PHP_EOL;
