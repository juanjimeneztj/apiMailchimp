<?php

// *********************************** 
// REQUIRED INFORMATION IMPORTANT
// ***********************************

// ****** Mailchimp API *******
$apikey = 'ENTER HERE YOUR API KEY';

// ****** List ID *******
$listId = 'ENTER HERE YOUR LIST ID FROM MAILCHIMP';

// *********************************** 
// END REQUIRED INFORMATION IMPORTANT
// ***********************************

error_reporting(0);

$email = (isset($_REQUEST['email'])) ? $_REQUEST['email'] : NULL; // Catch the email address

if ($email != NULL) {
    try {
        $memberId = md5(strtolower($email));
        $dataCenter = substr($apikey,strpos($apikey,'-')+1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

        $json = json_encode([
            'email_address' => $email,
            'status'        => 'subscribed'
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apikey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpCode == 200) {
            $response = [
                'code'    => 200,
                'message' => 'Email saved',
                'data'    => $data
            ];
        } else {
            $response = [
                'code'    => 404,
                'message' => 'Email not saved'
            ];
        }
    } catch (\Throwable $th) {
        $response = [
            'status'  => 'error',
            'message' => 'Mailchimp API failed',
            'code'    => 404
        ];
    }
}else{
    $response = [
        'status'  => 'error',
        'message' => 'Email invalido o null.',
        'code'    => 404
    ];
}

echo json_encode($response);
