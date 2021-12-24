<?php

function get_host()
{
    global $mode;
    if ($mode == 'production') {
        return 'https://www.billplz.com/api';
    } else {
        return 'https://www.billplz-sandbox.com/api';
    }
}

function get_api_key()
{
    global $api_key;
    return $api_key . ':';
}

function create_mpi($parameter)
{
    $process = curl_init(get_host() . '/v4/mass_payment_instructions');
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_USERPWD, get_api_key());
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($parameter));

    $return = curl_exec($process);
    $header = curl_getinfo($process, CURLINFO_HTTP_CODE);
    curl_close($process);

    return array($header, json_decode($return, true));
}

function get_bav($bank_account_number)
{
    $process = curl_init(get_host() . '/v3/bank_verification_services/'. $bank_account_number);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_USERPWD, get_api_key());
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);

    $return = curl_exec($process);
    $header = curl_getinfo($process, CURLINFO_HTTP_CODE);
    curl_close($process);

    return array($header, json_decode($return, true));
}

function create_bav($parameter)
{
    $process = curl_init(get_host() . '/v3/bank_verification_services');
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_USERPWD, get_api_key());
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($parameter));

    $return = curl_exec($process);
    $header = curl_getinfo($process, CURLINFO_HTTP_CODE);
    curl_close($process);

    return array($header, json_decode($return, true));
}

function create_po($parameter)
{
    $process = curl_init(get_host() . '/v4/payment_orders');
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_USERPWD, get_api_key());
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($parameter));

    $return = curl_exec($process);
    $header = curl_getinfo($process, CURLINFO_HTTP_CODE);
    curl_close($process);

    return array($header, json_decode($return, true));
}
