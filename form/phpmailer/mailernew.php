<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$config = require 'config.php';

$mail = new PHPMailer(true);

try {

    /* ================= SMTP SETTINGS ================= */
    $mail->isSMTP();
    $mail->Host = $config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['username'];
    $mail->Password = $config['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $config['port'];
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    /* ================= SANITIZE INPUT ================= */
    $firstname = htmlspecialchars($_POST['firstname'] ?? '');
    $lastname = htmlspecialchars($_POST['lastname'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $cell = htmlspecialchars($_POST['cell_phone'] ?? '');
    $work = htmlspecialchars($_POST['work_phone'] ?? '');
    $hasSpouse = htmlspecialchars($_POST['has_spouse'] ?? '');

    /* ================= MEMBERSHIP ================= */
    $membership = htmlspecialchars($_POST['question_1'] ?? '');
    if ($membership === 'other') {
        $membership = 'Other: $' . htmlspecialchars($_POST['other_membership'] ?? '');
    } else {
        $membership = '$' . $membership . '.00';
    }

    /* ================= ADMIN EMAIL ================= */
    $adminMessage = "
        <strong>New Membership Submission</strong><br><br>
        First Name: {$firstname}<br>
        Last Name: {$lastname}<br>
        Email: {$email}<br>
        Cell Phone: {$cell}<br>
        Work Phone: {$work}<br><br>
        Has Spouse: {$hasSpouse}<br>
    ";

    if ($hasSpouse === 'yes') {
        $adminMessage .= "
            Spouse First Name: " . htmlspecialchars($_POST['spouse_firstname'] ?? '') . "<br>
            Spouse Last Name: " . htmlspecialchars($_POST['spouse_lastname'] ?? '') . "<br>
            Spouse Email: " . htmlspecialchars($_POST['spouse_email'] ?? '') . "<br>
            Spouse Cell Phone: " . htmlspecialchars($_POST['spouse_cell_phone'] ?? '') . "<br>
            Spouse Work Phone: " . htmlspecialchars($_POST['spouse_work_phone'] ?? '') . "<br><br>
        ";
    }

    $adminMessage .= "Membership Fee: {$membership}";

    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress($config['to_email'], $config['to_name']);
    $mail->Subject = 'New DMV For Dessie Membership';
    $mail->Body = $adminMessage;
    $mail->send();  // Re-enabled sending

    /* ================= THANK YOU EMAIL ================= */
    $mail->clearAddresses();
    $mail->addAddress($email);
    $mail->Subject = 'Thank You for Your Membership';

    $thankYouMessage = "
        <p>Dear {$firstname},</p>

        <p>
        Thank you for becoming a member of <strong>DMV For Dessie</strong>.
        We truly appreciate your support and commitment to our mission.
        </p>

        <p>
        Your membership contribution of <strong>{$membership}</strong> helps us
        continue our work and make a meaningful difference in our community.
        </p>

        <p>
        If you have any questions or need assistance, feel free to reply to this email.
        We are grateful to have you as part of our community.
        </p>

        <p>
        Warm regards,<br>
        <strong>DMV For Dessie Team</strong>
        </p>
    ";

    $mail->Body = $thankYouMessage;
    $mail->send();  // Re-enabled sending

    /* ================= CALL NEXT.JS API ================= */
    $apiUrl = ($config['api_base_url'] ?? 'https://dmvfor-dessie-dashboard.vercel.app') . '/api/members';
    echo "<!-- API URL: {$apiUrl} -->";  // Debug line, can be removed later

    
    // Prepare fee data
    $rawTier = $_POST['question_1'] ?? '0';
    if ($rawTier === 'other') {
        $feeAmount = floatval($_POST['other_membership'] ?? 0);
    } else {
        $feeAmount = floatval(str_replace('$', '', $membership));
    }
    $feeTier = '$' . $rawTier;

    $apiData = [
        'firstName' => $firstname,
        'lastName' => $lastname,
        'email' => $email,
        'cellPhone' => $cell ?: null,
        'workPhone' => $work ?: null,
        'hasSpouse' => $hasSpouse === 'yes',
        'spouseFirstName' => ($hasSpouse === 'yes' && !empty($_POST['spouse_firstname'])) ? htmlspecialchars($_POST['spouse_firstname']) : null,
        'spouseLastName' => ($hasSpouse === 'yes' && !empty($_POST['spouse_lastname'])) ? htmlspecialchars($_POST['spouse_lastname']) : null,
        'spouseEmail' => ($hasSpouse === 'yes' && !empty($_POST['spouse_email'])) ? htmlspecialchars($_POST['spouse_email']) : null,
        'spouseCellPhone' => ($hasSpouse === 'yes' && !empty($_POST['spouse_cell_phone'])) ? htmlspecialchars($_POST['spouse_cell_phone']) : null,
        'spouseWorkPhone' => ($hasSpouse === 'yes' && !empty($_POST['spouse_work_phone'])) ? htmlspecialchars($_POST['spouse_work_phone']) : null,
        'feeTier' => $feeTier,
        'feeAmount' => $feeAmount,
    ];

    $jsonPayload = json_encode($apiData);

    /**
     * Send a POST request; try cURL first, then fallback to file_get_contents.
     */
    function postJson($url, $jsonPayload)
    {
        // 1. Try cURL
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_FOLLOWLOCATION => true,  // Follow 308/301 redirects
                
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            // curl_close() is a no-op since PHP 8.0, deprecated in 8.5 – just don't call it
            return [
                'http_code' => $httpCode,
                'response' => $response,
                'error' => $error,
                'method' => 'cURL'
            ];
        }

        // 2. Fallback to file_get_contents
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $jsonPayload,
                'timeout' => 5,
                'follow_location' => true,  // Follow redirects for stream context
            ],
           
        ];
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        // Extract HTTP code using non‑deprecated function
        $httpCode = null;
        if ($response !== false) {
            $headers = http_get_last_response_headers();
            if ($headers) {
                $statusLine = $headers[0] ?? '';
                preg_match('{HTTP\/\S*\s(\d{3})}', $statusLine, $match);
                $httpCode = $match[1] ?? null;
            }
        }

        return [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => ($response === false) ? error_get_last()['message'] ?? 'Unknown stream error' : null,
            'method' => 'file_get_contents'
        ];
    }

    $result = postJson($apiUrl, $jsonPayload);

    // Log or output the result for debugging
    if ($result['http_code'] != 201) {
        $logMsg = "API call failed ({$result['method']}). HTTP: {$result['http_code']}, Response: {$result['response']}, Error: {$result['error']}";
        error_log($logMsg);
        // Uncomment next line only while testing
        // echo "<!-- {$logMsg} -->";
    }

    /* ================= SUCCESS MESSAGE ================= */
    echo '
    <div id="success">
        <div class="icon icon--order-success svg">
            <svg xmlns="http://www.w3.org/2000/svg" width="72px" height="72px">
                <g fill="none" stroke="#8EC343" stroke-width="2">
                    <circle cx="36" cy="36" r="35"></circle>
                    <path d="M17.417,37.778l9.93,9.909l25.444-25.393"></path>
                </g>
            </svg>
        </div>
        <h4><span>Thank you for joining!</span></h4>
        <small>You will be redirected to the homepage in 5 seconds.</small>
    </div>

    <script>
        setTimeout(function () {
            window.location.href = "/form/index-4.html";
        }, 5000);
    </script>
    ';

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

/* ================= GOOGLE SHEETS ================= */
$sheetUrl = 'https://script.google.com/macros/s/AKfycbxb2pC__2RL6KLGin8_FTX8J075ZQh5jqyk-WAJR83LZbRHA-8BHSM9gPPyR2YvAaBx/exec';

$data = [
    'firstname' => $firstname,
    'lastname' => $lastname,
    'email' => $email,
    'cell_phone' => $cell,
    'work_phone' => $work,
    'has_spouse' => $hasSpouse,
    'membership' => $membership
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data),
        'timeout' => 5
    ]
];

// $context = stream_context_create($options);
// file_get_contents($sheetUrl, false, $context);  // Re-enabled Google Sheets
?>