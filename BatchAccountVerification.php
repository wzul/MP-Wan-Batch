<?php

require 'includes/head.php';
require 'includes/api_call.php';

echo 'Make sure you set to UTF-8 character encoding on the file! ' . PHP_EOL;
echo 'The expected arrangement is: Bank Code, Bank Account Number, ID Number, Bank Account Holder Name, Total, Description, Email, ID, Status, MP Collection ID';
echo PHP_EOL . PHP_EOL;

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
        'name' => $data[3],
        'id_no' => $data[2],
        'acc_no' => $data[1],
        'code' => $data[0],
        'organization' => 'false'
    );
}

$response = array();

echo 'Sending to Billplz API...' . PHP_EOL . PHP_EOL;

foreach ($bank_account as $bank) {
    $account_info = get_bav($bank['acc_no']);

    if (isset($account_info[1]['status'])) {
        if ($account_info[1]['status'] !== 'pending' && $account_info[1]['status'] !== 'verified') {
            $response[] = create_bav($bank);
        } else {
            $response[] = [200, ['status' => 'account_already_verified']];
        }
    } else {
        $response[] = create_bav($bank);
    }
}

echo 'Writing to csv file...' . PHP_EOL . PHP_EOL;

for ($i = 0; $i < sizeof($response); $i++) {
    if ($response[$i][0] === 200) {
        $array[$i + 1][8] = $response[$i][1]['status'];
    }
}

$fp = fopen($filename, 'w');
foreach ($array as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
