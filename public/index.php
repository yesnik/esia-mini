<?php

require __DIR__ . '/../vendor/autoload.php';

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

$esia = new \Esia\OpenId($config);
?>

<a href="<?= $esia->buildUrl() ?>">Войти через портал Госуслуги</a>
