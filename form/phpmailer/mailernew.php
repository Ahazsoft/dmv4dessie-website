<?php

/*
|--------------------------------------------------------------------------
| PHPMailer
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/src/Exception.php';
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';

$config = require __DIR__ . '/config.php';


/*
|--------------------------------------------------------------------------
| STRIPE PAYMENT LINKS
|--------------------------------------------------------------------------
*/

$paymentLinks = [
    '20' => 'https://buy.stripe.com/aFabJ23udeIvf9xgaJ8Vi07',
    '30' => 'https://buy.stripe.com/8x2cN6fcVeIv5yXbUt8Vi08',
    '50' => 'https://buy.stripe.com/9B69AUaWF2ZNaTh9Ml8Vi09',
];


/*
|--------------------------------------------------------------------------
| SEND JSON POST REQUEST
|--------------------------------------------------------------------------
*/

function postJson($url, $jsonPayload)
{
    // -----------------------------------------------------------------
    // cURL
    // -----------------------------------------------------------------
    if (function_exists('curl_init')) {

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,

            // TEMPORARY DEV WORKAROUND — REMOVE BEFORE PRODUCTION
            // CURLOPT_SSL_VERIFYPEER => false,
            // CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        return [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error,
            'method' => 'cURL',
        ];
    }

    // -----------------------------------------------------------------
    // file_get_contents fallback
    // -----------------------------------------------------------------
    $options = [
        'http' => [
            'method' => 'POST',
            'header' =>
                "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n",
            'content' => $jsonPayload,
            'timeout' => 15,
            'follow_location' => true,
            'ignore_errors' => true,
        ],

        // ✅ 'ssl' is a sibling of 'http'
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    $httpCode = null;
    $headers = http_get_last_response_headers();

    if ($headers && is_array($headers)) {
        foreach ($headers as $header) {
            if (preg_match('/HTTP\/\S+\s+(\d{3})/', $header, $m)) {
                $httpCode = (int) $m[1];
            }
        }
    }

    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => ($response === false)
            ? (error_get_last()['message'] ?? 'Unknown stream error')
            : null,
        'method' => 'file_get_contents',
    ];
}


/*
|--------------------------------------------------------------------------
| ERROR PAGE
|--------------------------------------------------------------------------
*/

function renderError($message)
{
    $safeMessage = htmlspecialchars(
        $message,
        ENT_QUOTES,
        'UTF-8'
    );

    echo '
    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>
            Membership Error | DMV For Dessie
        </title>

        <style>

            body {
                margin: 0;
                min-height: 100vh;

                display: flex;
                align-items: center;
                justify-content: center;

                background: #f7f7f7;

                font-family: Arial, sans-serif;

                color: #333;

                padding: 20px;

                box-sizing: border-box;
            }

            .card {
                width: 100%;
                max-width: 560px;

                background: #fff;

                padding: 40px;

                text-align: center;

                border-radius: 12px;

                box-shadow:
                    0 8px 30px rgba(0,0,0,.08);
            }

            .icon {
                width: 72px;
                height: 72px;

                margin: 0 auto 20px;

                border-radius: 50%;

                border: 2px solid #d9534f;

                display: flex;
                align-items: center;
                justify-content: center;

                color: #d9534f;

                font-size: 36px;
            }

            h2 {
                margin-bottom: 12px;
            }

            p {
                line-height: 1.6;
                color: #666;
            }

            .button {
                display: inline-block;

                margin-top: 20px;

                padding: 12px 22px;

                border-radius: 6px;

                background: #7BC816;

                color: #fff;

                text-decoration: none;
            }

        </style>

    </head>

    <body>

        <div class="card">

            <div class="icon">
                !
            </div>

            <h2>
                Something went wrong
            </h2>

            <p>
                ' . $safeMessage . '
            </p>

            <a
                class="button"
                href="../index.html"
            >
                Go Back
            </a>

        </div>

    </body>

    </html>
    ';
}


/*
|--------------------------------------------------------------------------
| CUSTOM MEMBERSHIP PAGE
|--------------------------------------------------------------------------
*/

function renderCustomMembership(
    $firstname,
    $membership
) {

    $safeFirstname = htmlspecialchars(
        $firstname,
        ENT_QUOTES,
        'UTF-8'
    );

    $safeMembership = htmlspecialchars(
        $membership,
        ENT_QUOTES,
        'UTF-8'
    );

    echo '
    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>
            Membership Submitted | DMV For Dessie
        </title>

        <style>

            body {
                margin: 0;

                min-height: 100vh;

                display: flex;

                align-items: center;

                justify-content: center;

                background: #f7f7f7;

                font-family: Arial, sans-serif;

                color: #333;

                padding: 20px;

                box-sizing: border-box;
            }

            .card {

                width: 100%;

                max-width: 620px;

                background: #fff;

                padding: 45px;

                text-align: center;

                border-radius: 12px;

                box-shadow:
                    0 8px 30px rgba(0,0,0,.08);
            }

            .icon {

                width: 72px;

                height: 72px;

                margin: 0 auto 20px;

                border-radius: 50%;

                border: 2px solid #8EC343;

                display: flex;

                align-items: center;

                justify-content: center;

                color: #8EC343;

                font-size: 36px;
            }

            h2 {
                margin-bottom: 12px;
            }

            p {

                line-height: 1.7;

                color: #666;

                margin-bottom: 10px;
            }

            .amount {

                font-size: 22px;

                font-weight: 700;

                color: #333;

                margin: 20px 0;
            }

            .button {

                display: inline-block;

                margin-top: 20px;

                padding: 12px 22px;

                border-radius: 6px;

                background: #7BC816;

                color: #fff;

                text-decoration: none;
            }

        </style>

    </head>

    <body>

        <div class="card">

            <div class="icon">
                ✓
            </div>

            <h2>
                Thank You, ' . $safeFirstname . '!
            </h2>

            <p>
                Your DMV For Dessie membership request
                has been received successfully.
            </p>

            <p>
                You selected a custom membership contribution.
            </p>

            <div class="amount">
                ' . $safeMembership . '
            </div>

            <p>
                Our team will review your request and
                get back to you with the next steps.
            </p>

            <a
                class="button"
                href="../index.html"
            >
                Return to Homepage
            </a>

        </div>

    </body>

    </html>
    ';
}


/*
|--------------------------------------------------------------------------
| MAIN PROCESS
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE PHPMailer
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Use the FULL namespace here.
    | This fixes:
    |
    | Class "PHPMailer" not found
    |
    */

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);


    /*
    |--------------------------------------------------------------------------
    | SMTP SETTINGS
    |--------------------------------------------------------------------------
    */

    $mail->isSMTP();

    $mail->Host =
        $config['host'];

    $mail->SMTPAuth =
        true;

    $mail->Username =
        $config['username'];

    $mail->Password =
        $config['password'];

    $mail->SMTPSecure =
        \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port =
        $config['port'];

    $mail->isHTML(true);

    $mail->CharSet =
        'UTF-8';


    /*
    |--------------------------------------------------------------------------
    | FORM DATA
    |--------------------------------------------------------------------------
    */

    $firstname =
        trim($_POST['firstname'] ?? '');

    $lastname =
        trim($_POST['lastname'] ?? '');

    $email =
        trim($_POST['email'] ?? '');

    $cell =
        trim($_POST['cell_phone'] ?? '');

    $work =
        trim($_POST['work_phone'] ?? '');

    $hasSpouse =
        trim($_POST['has_spouse'] ?? '');

    $rawTier =
        trim($_POST['question_1'] ?? '');

    $otherMembership =
        trim($_POST['other_membership'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        $firstname === '' ||
        $lastname === ''
    ) {

        renderError(
            'Please provide your first name and last name.'
        );

        exit;
    }


    if (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        renderError(
            'Please provide a valid email address.'
        );

        exit;
    }


    if (
        !in_array(
            $hasSpouse,
            ['yes', 'no'],
            true
        )
    ) {

        renderError(
            'Please specify whether you have a spouse.'
        );

        exit;
    }


    if (
        !in_array(
            $rawTier,
            ['20', '30', '50', 'other'],
            true
        )
    ) {

        renderError(
            'Please select a valid membership fee.'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP FEE
    |--------------------------------------------------------------------------
    */

    if ($rawTier === 'other') {

        $feeAmount =
            (float) $otherMembership;


        if ($feeAmount <= 0) {

            renderError(
                'Please enter a valid custom membership amount.'
            );

            exit;
        }


        $feeTier =
            'Custom';


        $membership =
            'Other: $' .
            number_format(
                $feeAmount,
                2
            );

    } else {

        $feeAmount =
            (float) $rawTier;


        $feeTier =
            '$' . $rawTier;


        $membership =
            '$' .
            number_format(
                $feeAmount,
                2
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SPOUSE DATA
    |--------------------------------------------------------------------------
    */

    $spouseFirstName =
        null;

    $spouseLastName =
        null;

    $spouseEmail =
        null;

    $spouseCellPhone =
        null;

    $spouseWorkPhone =
        null;


    if ($hasSpouse === 'yes') {

        $spouseFirstName =
            trim(
                $_POST['spouse_firstname'] ?? ''
            );

        $spouseLastName =
            trim(
                $_POST['spouse_lastname'] ?? ''
            );

        $spouseEmail =
            trim(
                $_POST['spouse_email'] ?? ''
            );

        $spouseCellPhone =
            trim(
                $_POST['spouse_cell_phone'] ?? ''
            );

        $spouseWorkPhone =
            trim(
                $_POST['spouse_work_phone'] ?? ''
            );


        if (
            $spouseEmail !== '' &&
            !filter_var(
                $spouseEmail,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            renderError(
                'Please provide a valid spouse email address.'
            );

            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE MEMBER FIRST
    |--------------------------------------------------------------------------
    */

    $apiUrl =
        (
            $config['api_base_url']
            ?? 'https://dmvfor-dessie-dashboard.vercel.app'
        )
        . '/api/members';


    $apiData = [

        'firstName' =>
            $firstname,

        'lastName' =>
            $lastname,

        'email' =>
            $email,

        'cellPhone' =>
            $cell !== ''
            ? $cell
            : null,

        'workPhone' =>
            $work !== ''
            ? $work
            : null,

        'hasSpouse' =>
            $hasSpouse === 'yes',

        'spouseFirstName' =>
            $spouseFirstName,

        'spouseLastName' =>
            $spouseLastName,

        'spouseEmail' =>
            $spouseEmail,

        'spouseCellPhone' =>
            $spouseCellPhone,

        'spouseWorkPhone' =>
            $spouseWorkPhone,

        'feeTier' =>
            $feeTier,

        'feeAmount' =>
            $feeAmount,
    ];


    /*
    |--------------------------------------------------------------------------
    | JSON ENCODE
    |--------------------------------------------------------------------------
    */

    $jsonPayload =
        json_encode(
            $apiData
        );


    if ($jsonPayload === false) {

        error_log(
            'Failed to encode member API payload.'
        );

        renderError(
            'We could not process your membership request.'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | CALL NEXT.JS API
    |--------------------------------------------------------------------------
    */

    $result =
        postJson(
            $apiUrl,
            $jsonPayload
        );


    /*
    |--------------------------------------------------------------------------
    | CHECK API RESPONSE
    |--------------------------------------------------------------------------
    */

    if (
        (int) $result['http_code'] !== 201
    ) {

        error_log(
            'Member API call failed. ' .
            'Method: ' .
            $result['method'] .
            ', HTTP: ' .
            $result['http_code'] .
            ', Response: ' .
            $result['response'] .
            ', Error: ' .
            $result['error']
        );


        $responseData =
            json_decode(
                $result['response'] ?? '',
                true
            );


        /*
        |--------------------------------------------------------------------------
        | DUPLICATE EMAIL
        |--------------------------------------------------------------------------
        */

        if (
            (int) $result['http_code'] === 409
        ) {

            renderError(
                $responseData['error']
                ??
                'A member with this email already exists.'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | OTHER API ERROR
        |--------------------------------------------------------------------------
        */

        renderError(
            'We could not create your membership at this time. Please try again later.'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET CREATED MEMBER
    |--------------------------------------------------------------------------
    |
    | /api/members returns the created member directly.
    |
    | Therefore:
    |
    | $member['id']
    |
    | is the newly created member ID.
    |
    */

    $member =
        json_decode(
            $result['response'] ?? '',
            true
        );


    if (
        !is_array($member) ||
        empty($member['id'])
    ) {

        error_log(
            'Invalid member API response: ' .
            ($result['response'] ?? '')
        );

        renderError(
            'The membership was created, but the member ID could not be retrieved.'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | THIS IS THE MEMBER ID
    |--------------------------------------------------------------------------
    */

    $memberId =
        $member['id'];


    error_log(
        "Member created successfully. " .
        "Member ID: {$memberId}"
    );


    /*
    |--------------------------------------------------------------------------
    | ESCAPED VALUES FOR EMAIL HTML
    |--------------------------------------------------------------------------
    */

    $safeFirstname =
        htmlspecialchars(
            $firstname,
            ENT_QUOTES,
            'UTF-8'
        );

    $safeLastname =
        htmlspecialchars(
            $lastname,
            ENT_QUOTES,
            'UTF-8'
        );

    $safeEmail =
        htmlspecialchars(
            $email,
            ENT_QUOTES,
            'UTF-8'
        );

    $safeCell =
        htmlspecialchars(
            $cell,
            ENT_QUOTES,
            'UTF-8'
        );

    $safeWork =
        htmlspecialchars(
            $work,
            ENT_QUOTES,
            'UTF-8'
        );

    $safeHasSpouse =
        htmlspecialchars(
            $hasSpouse,
            ENT_QUOTES,
            'UTF-8'
        );

    $safeMembership =
        htmlspecialchars(
            $membership,
            ENT_QUOTES,
            'UTF-8'
        );

    $safeMemberId =
        htmlspecialchars(
            $memberId,
            ENT_QUOTES,
            'UTF-8'
        );


    /*
    |--------------------------------------------------------------------------
    | ADMIN EMAIL CONTENT
    |--------------------------------------------------------------------------
    */

    $adminMessage = "

        <strong>
            New Membership Submission
        </strong>

        <br><br>

        First Name:
        {$safeFirstname}

        <br>

        Last Name:
        {$safeLastname}

        <br>

        Email:
        {$safeEmail}

        <br>

        Cell Phone:
        {$safeCell}

        <br>

        Work Phone:
        {$safeWork}

        <br><br>

        Has Spouse:
        {$safeHasSpouse}

        <br>

    ";


    if ($hasSpouse === 'yes') {

        $adminMessage .= "

            Spouse First Name:
            " .
            htmlspecialchars(
                $spouseFirstName ?? '',
                ENT_QUOTES,
                'UTF-8'
            )
            . "
            <br>

            Spouse Last Name:
            " .
            htmlspecialchars(
                $spouseLastName ?? '',
                ENT_QUOTES,
                'UTF-8'
            )
            . "
            <br>

            Spouse Email:
            " .
            htmlspecialchars(
                $spouseEmail ?? '',
                ENT_QUOTES,
                'UTF-8'
            )
            . "
            <br>

            Spouse Cell Phone:
            " .
            htmlspecialchars(
                $spouseCellPhone ?? '',
                ENT_QUOTES,
                'UTF-8'
            )
            . "
            <br>

            Spouse Work Phone:
            " .
            htmlspecialchars(
                $spouseWorkPhone ?? '',
                ENT_QUOTES,
                'UTF-8'
            )
            . "
            <br><br>

        ";
    }


    $adminMessage .= "

        Membership Tier:
        {$safeMembership}

        <br>

        Member ID:
        {$safeMemberId}

    ";


    /*
    |--------------------------------------------------------------------------
    | SEND ADMIN EMAIL
    |--------------------------------------------------------------------------
    */

    $mail->clearAddresses();

    $mail->setFrom(
        $config['from_email'],
        $config['from_name']
    );

    $mail->addAddress(
        $config['to_email'],
        $config['to_name']
    );

    $mail->Subject =
        'New DMV For Dessie Membership';

    $mail->Body =
        $adminMessage;


    try {

        $mail->send();

    } catch (
        \PHPMailer\PHPMailer\Exception $e
    ) {

        error_log(
            'Admin membership email failed: ' .
            $mail->ErrorInfo
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBER THANK YOU EMAIL
    |--------------------------------------------------------------------------
    */

    $mail->clearAddresses();

    $mail->addAddress(
        $email
    );

    $mail->Subject =
        'Thank You for Your Membership';


    $thankYouMessage = "

        <p>
            Dear {$safeFirstname},
        </p>

        <p>

            Thank you for becoming a member of
            <strong>DMV For Dessie</strong>.

            We truly appreciate your support
            and commitment to our mission.

        </p>

        <p>

            Your membership contribution of
            <strong>{$safeMembership}</strong>

            helps us continue our work and make
            a meaningful difference in our community.

        </p>

    ";


    if ($rawTier !== 'other') {

        $thankYouMessage .= "

            <p>

                To complete your membership,
                please proceed to the payment page
                after this submission.

            </p>

        ";

    } else {

        $thankYouMessage .= "

            <p>

                Your custom membership contribution
                has been received.

                Our team will contact you
                with the next steps.

            </p>

        ";
    }


    $thankYouMessage .= "

        <p>

            If you have any questions or need
            assistance, feel free to reply
            to this email.

        </p>

        <p>

            Warm regards,<br>

            <strong>
                DMV For Dessie Team
            </strong>

        </p>

    ";


    $mail->Body =
        $thankYouMessage;


    try {

        $mail->send();

    } catch (
        \PHPMailer\PHPMailer\Exception $e
    ) {

        error_log(
            'Member thank-you email failed: ' .
            $mail->ErrorInfo
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOM MEMBERSHIP
    |--------------------------------------------------------------------------
    |
    | The member has already been created as PENDING.
    |
    | No Stripe redirect for custom memberships.
    |
    */

    if ($rawTier === 'other') {

        renderCustomMembership(
            $firstname,
            $membership
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | FIXED MEMBERSHIP → STRIPE PAYMENT LINK
    |--------------------------------------------------------------------------
    */

    $paymentUrl =
        $paymentLinks[$rawTier] ?? null;


    if (!$paymentUrl) {

        renderError(
            'No payment link is configured for this membership tier.'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | REDIRECT TO STRIPE
    |--------------------------------------------------------------------------
    |
    | For now we are simply redirecting to the Payment Link.
    |
    | Later, when Stripe access is available, we will attach:
    |
    | client_reference_id = $memberId
    |
    | and handle the webhook.
    |
    */

    header(
        'Location: ' . $paymentUrl,
        true,
        303
    );

    exit;


} catch (
    \PHPMailer\PHPMailer\Exception $e
) {

    /*
    |--------------------------------------------------------------------------
    | PHPMailer ERROR
    |--------------------------------------------------------------------------
    */

    error_log(
        'PHPMailer error: ' .
        $e->getMessage()
    );

    renderError(
        'We could not process your membership request. Please try again later.'
    );

    exit;


} catch (
    \Throwable $e
) {

    /*
    |--------------------------------------------------------------------------
    | GENERAL ERROR
    |--------------------------------------------------------------------------
    */

    error_log(
        'Membership processing error: ' .
        $e->getMessage()
    );

    renderError(
        'We could not process your membership request. Please try again later.'
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| GOOGLE SHEETS
|--------------------------------------------------------------------------
|
| Currently disabled.
|
*/

$sheetUrl =
    'https://script.google.com/macros/s/AKfycbxb2pC__2RL6KLGin8_FTX8J075ZQh5jqyk-WAJR83LZbRHA-8BHSM9gPPyR2YvAaBx/exec';


$data = [

    'firstname' =>
        $firstname ?? '',

    'lastname' =>
        $lastname ?? '',

    'email' =>
        $email ?? '',

    'cell_phone' =>
        $cell ?? '',

    'work_phone' =>
        $work ?? '',

    'has_spouse' =>
        $hasSpouse ?? '',

    'membership' =>
        $membership ?? '',
];


$options = [

    'http' => [

        'header' =>
            "Content-Type: application/json\r\n",

        'method' =>
            'POST',

        'content' =>
            json_encode($data),

        'timeout' =>
            5,
    ],
];


/*
|--------------------------------------------------------------------------
| DISABLED FOR NOW
|--------------------------------------------------------------------------
*/

// $context = stream_context_create($options);
// file_get_contents($sheetUrl, false, $context);

?>
