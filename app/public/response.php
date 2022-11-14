<?php

use Esia\Http\GuzzleHttpClient;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

print_r($_GET);

$config = new \Esia\Config([
    'clientId' => 'PLACE-HERE-YOUR-SYSTEM-MNEMONIC', // Мнемоника вашей системы
    'redirectUrl' => 'http://127.0.0.1:8000/response.php',
    'portalUrl' => 'https://esia-portal1.test.gosuslugi.ru/',
    'scope' => [
        'fullname',
        'birthdate',
        'birthplace',
        'email',
        'mobile',
        'contacts',
        'id_doc',
        'snils',
        'inn',
    ],
    'certPath' => __DIR__ . '/../resources/ekapusta.gost.test.cer',
    'privateKeyPath' => __DIR__ . '/../resources/ekapusta.gost.test.key',
]);

$client = new GuzzleHttpClient(
    new Client([
        'verify' => false
    ])
);
$esia = new \Esia\OpenId($config, $client);

$token = $esia->getToken($_GET['code']);

$personInfo = $esia->getPersonInfo();
$addressInfo = $esia->getAddressInfo();
$contactInfo = $esia->getContactInfo();
$documentInfo = $esia->getDocInfo();

echo '<pre>';
print_r($personInfo);
print_r($addressInfo);
print_r($contactInfo);
print_r($documentInfo);
