<?php

require 'includes/head.php';
require 'includes/api_call.php';

echo 'Make sure you set to UTF-8 character encoding on the file! ' . PHP_EOL;
echo 'The expected arrangement is: Bank Code, Bank Account Number, ID Number, Bank Account Holder Name, Total, Description, Email, ID, Status, MP Collection ID, Uniq Ref ID';
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

    $parameter = array(
        'mass_payment_instruction_collection_id' => trim($data[9]),
        'bank_code' => trim($data[0]),
        'bank_account_number' => trim($data[1]),
        'identity_number' => trim($data[2]),
        'name' => trim($data[3]),
        'description' => trim($data[5]),
        'total' => strval(preg_replace("/[^0-9.]/", "", $data[4]) * 100)
    );
    $optional = array(
        'email' => $data[6],
        'recipient_notification' => 'true',
        'notification' => 'true',
        'reference_id' => $data[10],
    );

    $bank_account[] = array(
        'parameter' => $parameter,
        'optional' => $optional,
        'id' => $data[7]
    );
}

$response = array();

echo 'Sending to Billplz API...' . PHP_EOL . PHP_EOL;

foreach ($bank_account as $bank) {
    if (empty($bank['id'])) {
        $response[] = create_po(array_merge($bank['parameter'], $bank['optional']));
    }
}

echo 'Writing to csv file...' . PHP_EOL . PHP_EOL;

for ($i = 0; $i < sizeof($response); $i++) {
    if ($response[$i][0] === 200) {
        $array[$i + 1][7] = $response[$i][1]['id'];
        $array[$i + 1][8] = $response[$i][1]['status'];
    } else {
        if (is_array($response[$i][1]['error']['message'])) {
            $array[$i + 1][8] = implode(',', $response[$i][1]['error']['message']);
        } else {
            $array[$i + 1][8] = $response[$i][1]['error']['message'];
        }
    }
}

echo "Generating Success Files... (success_{$filename})" . PHP_EOL;

$fp = fopen('success_' . $filename, 'w');
foreach ($array as $fields) {
    if (!empty($fields[7])) {
        fputcsv($fp, $fields);
    }
}
fclose($fp);
echo 'Success...' . PHP_EOL . PHP_EOL;

echo "Generating Failed Files... (failed_{$filename})" . PHP_EOL;

$printheader = true;
$fp = fopen('failed_' . $filename, 'w');
foreach ($array as $fields) {
    if (empty($fields[7]) || $printheader) {
        fputcsv($fp, $fields);
        $printheader = false;
    }
}
fclose($fp);

echo 'Success...' . PHP_EOL . PHP_EOL;
