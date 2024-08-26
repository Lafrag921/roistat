<?php
require_once 'access.php';
use GuzzleHttp\Client;
require 'vendor/autoload.php';

$client = new Client([
    'base_uri' => 'https://'.$subdomain.'.amocrm.ru/api/v4/',
    'headers' => [
        'Authorization' => 'Bearer ' . $access_token,
        'Content-Type' => 'application/json'
    ]
]);


$name = $_POST["name"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$price = $_POST["price"];
$spentMoreThan30Seconds = $_POST["30sec"] ? true : false;
$custom_field_id = 1093757;

try {
    $contactResponse = $client->post('contacts', [
        'json' => [
            'name' => $name,
            'custom_fields_values' => [
                [
                    'field_code' => 'EMAIL',
                    'values' => [
                        ['value' => $email]
                    ]
                ],
                [
                    'field_code' => 'PHONE',
                    'values' => [
                        ['value' => $phone]
                    ]
                ]
            ]
        ]
    ]);

    $contactData = json_decode($contactResponse->getBody(), true);
    $contactId = $contactData['_embedded']['contacts'][0]['id'];

    $leadResponse = $client->post('leads', [
        'json' => [
            'name' => 'Новая сделка',
            'price' => (int)$price,
            'custom_fields_values' => [
                [
                    'field_id' => $custom_field_id,
                    'values' => [
                        ['value' => $spentMoreThan30Seconds]
                    ]
                ]
            ],
            '_embedded' => [
                'contacts' => [
                    ['id' => $contactId]
                ]
            ]
        ]
    ]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка']);
}