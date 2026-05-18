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
    $mail->Host       = $config['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['username'];
    $mail->Password   = $config['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $config['port'];
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    /* ================= SANITIZE INPUT ================= */
    $firstname = htmlspecialchars($_POST['firstname'] ?? '');
    $lastname  = htmlspecialchars($_POST['lastname'] ?? '');
    $email     = htmlspecialchars($_POST['email'] ?? '');
    $cell      = htmlspecialchars($_POST['cell_phone'] ?? '');
    $work      = htmlspecialchars($_POST['work_phone'] ?? '');
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
    $mail->send();

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
    $mail->send();

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
            window.location.href = "/index-4.html";
        }, 5000);
    </script>
    ';

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

/* ================= CALL NEXT.JS API ================= */

$apiUrl = ($config['api_base_url'] ?? 'https://yourdomain.com') . '/api/members';

// Prepare fee data from the membership string
$rawTier = $_POST['question_1'] ?? '0';
if ($rawTier === 'other') {
    $feeAmount = floatval($_POST['other_membership'] ?? 0);
} else {
    // $membership is already formatted like '$25.00'
    $feeAmount = floatval(str_replace('$', '', $membership));
}
$feeTier = $rawTier;

// Build the JSON payload exactly matching the CreateMemberSchema
$apiData = [
    'firstName'   => $firstname,
    'lastName'    => $lastname,
    'email'       => $email,
    'cellPhone'   => $cell ?: null,
    'workPhone'   => $work ?: null,
    'hasSpouse'   => $hasSpouse === 'yes',
    'spouseFirstName' => ($hasSpouse === 'yes' && !empty($_POST['spouse_firstname'])) ? htmlspecialchars($_POST['spouse_firstname']) : null,
    'spouseLastName'  => ($hasSpouse === 'yes' && !empty($_POST['spouse_lastname'])) ? htmlspecialchars($_POST['spouse_lastname']) : null,
    'spouseEmail'     => ($hasSpouse === 'yes' && !empty($_POST['spouse_email'])) ? htmlspecialchars($_POST['spouse_email']) : null,
    'spouseCellPhone' => ($hasSpouse === 'yes' && !empty($_POST['spouse_cell_phone'])) ? htmlspecialchars($_POST['spouse_cell_phone']) : null,
    'spouseWorkPhone' => ($hasSpouse === 'yes' && !empty($_POST['spouse_work_phone'])) ? htmlspecialchars($_POST['spouse_work_phone']) : null,
    'feeTier'    => $feeTier,
    'feeAmount'  => $feeAmount,
];

// Send to the API using cURL
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => json_encode($apiData),
    CURLOPT_TIMEOUT        => 5,
]);

$apiResponse = curl_exec($ch);
$httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError   = curl_error($ch);
curl_close($ch);

// If the API didn't return 201 Created, log the failure (does not stop the script)
if ($httpCode !== 201) {
    error_log("DMV API call failed. HTTP $httpCode. Response: " . $apiResponse . " Error: " . $curlError);
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
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'timeout' => 5
    ]
];

$context = stream_context_create($options);
file_get_contents($sheetUrl, false, $context);

?>

