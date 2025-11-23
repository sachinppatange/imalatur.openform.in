<?php

include_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mobile = $_POST['mobile'];

    // Step 1: Check if the mobile number exists in the database
    // $sql = "SELECT * FROM student_registration WHERE mobile = '$mobile'";
    // $result = mysqli_query($GLOBALS['conn'], $sql);
    // $count = mysqli_num_rows($result);
    // if ($count > 0) {


        // Step 1: Generate the OTP
        $otp = rand(100000, 999999);

        // Step 2: Set up the cURL request
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode(
                    array(
                        "integrated_number" => "918793463943",
                        "content_type" => "template",
                        "payload" => array(
                            "messaging_product" => "whatsapp",
                            "type" => "template",
                            "template" => array(
                                "name" => "otp01",
                                "language" => array(
                                    "code" => "en",
                                    "policy" => "deterministic"
                                ),
                                "namespace" => null,
                                "to_and_components" => array(
                                    array(
                                        "to" => [$mobile],
                                        "components" => array(
                                            "body_1" => array(
                                                "type" => "text",
                                                "value" => $otp // Insert the OTP here
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                ),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'authkey: 417836Ar8HRlrqs666d72f2P1',
                ),
            )
        );

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode == 200) {
            echo json_encode(array('success' => true, 'otp' => $otp));
        } else {
            echo json_encode(array('success' => false));
        }
    // } else {
    //     Mobile number does not exist
    //     echo json_encode(array('success' => false, 'message' => 'This number is not in our Registered Database.'));
    // }
}
?>