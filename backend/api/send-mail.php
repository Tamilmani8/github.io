<?php

/*
|--------------------------------------------------------------------------
| CORS + JSON
|--------------------------------------------------------------------------
*/

header("Content-Type: application/json; charset=UTF-8");

header(
    "Access-Control-Allow-Origin: https://printdeal.co.in"
);

header(
    "Access-Control-Allow-Methods: POST, OPTIONS"
);

header(
    "Access-Control-Allow-Headers: Content-Type"
);


/*
|--------------------------------------------------------------------------
| OPTIONS request
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}


/*
|--------------------------------------------------------------------------
| Only POST allowed
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Get request data
|--------------------------------------------------------------------------
|
| Manpower form:
| application/json
|
| Candidate form:
| multipart/form-data
|
|--------------------------------------------------------------------------
*/

$contentType = $_SERVER["CONTENT_TYPE"] ?? "";

if (
    stripos(
        $contentType,
        "application/json"
    ) !== false
) {

    $rawData = file_get_contents("php://input");

    $data = json_decode(
        $rawData,
        true
    );

    if (!is_array($data)) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid JSON data."
        ]);

        exit;
    }

} else {

    $data = $_POST;
}


/*
|--------------------------------------------------------------------------
| Form Type
|--------------------------------------------------------------------------
*/

$formType = $data["formType"] ?? "";


/*
|--------------------------------------------------------------------------
| MANPOWER FORM
|--------------------------------------------------------------------------
*/

if ($formType === "manpower") {

    /*
    |--------------------------------------------------------------------------
    | Get fields
    |--------------------------------------------------------------------------
    */

    $companyName = trim(
        $data["companyName"] ?? ""
    );

    $contactPerson = trim(
        $data["contactPerson"] ?? ""
    );

    $email = trim(
        $data["email"] ?? ""
    );

    $phone = trim(
        $data["phone"] ?? ""
    );

    $requiredWorkers = trim(
        $data["requiredWorkers"] ?? ""
    );

    $requirementDetails = trim(
        $data["requirementDetails"] ?? ""
    );


    /*
    |--------------------------------------------------------------------------
    | Required field validation
    |--------------------------------------------------------------------------
    */

    if (
        empty($companyName) ||
        empty($contactPerson) ||
        empty($email) ||
        empty($phone) ||
        empty($requiredWorkers) ||
        empty($requirementDetails)
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Please fill all required fields."
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Email validation
    |--------------------------------------------------------------------------
    */

    if (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid email address."
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Worker validation
    |--------------------------------------------------------------------------
    */

    if (
        !is_numeric($requiredWorkers) ||
        $requiredWorkers < 1
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid worker count."
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Email configuration
    |--------------------------------------------------------------------------
    */

    $to = "admin@skhrservices.in";

    $subject =
        "New Manpower Requirement - Printdeal";


    /*
    |--------------------------------------------------------------------------
    | Email body
    |--------------------------------------------------------------------------
    */

    $body = "";

    $body .= "NEW MANPOWER REQUIREMENT";
    $body .= "\r\n";
    $body .= "========================";
    $body .= "\r\n\r\n";

    $body .= "Company Name: ";
    $body .= $companyName;
    $body .= "\r\n";

    $body .= "Contact Person: ";
    $body .= $contactPerson;
    $body .= "\r\n";

    $body .= "Email: ";
    $body .= $email;
    $body .= "\r\n";

    $body .= "Contact Number: ";
    $body .= $phone;
    $body .= "\r\n";

    $body .= "Required Workers: ";
    $body .= $requiredWorkers;
    $body .= "\r\n\r\n";

    $body .= "Requirement Details:";
    $body .= "\r\n";

    $body .= $requirementDetails;
    $body .= "\r\n";


    /*
    |--------------------------------------------------------------------------
    | Headers
    |--------------------------------------------------------------------------
    */

    $headers = "";

    $headers .=
        "From: website@printdeal.co.in\r\n";

    $headers .=
        "Reply-To: " . $email . "\r\n";

    $headers .=
        "MIME-Version: 1.0\r\n";

    $headers .=
        "Content-Type: text/plain; charset=UTF-8\r\n";


    /*
    |--------------------------------------------------------------------------
    | Send email
    |--------------------------------------------------------------------------
    */

    $sent = mail(
        $to,
        $subject,
        $body,
        $headers
    );


    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    if ($sent) {

        echo json_encode([
            "success" => true,
            "message" =>
                "Manpower requirement submitted successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" =>
                "Unable to send email."
        ]);
    }

    exit;
}


/*
|--------------------------------------------------------------------------
| CANDIDATE FORM
|--------------------------------------------------------------------------
*/

if ($formType === "candidate") {

    /*
    |--------------------------------------------------------------------------
    | Get fields
    |--------------------------------------------------------------------------
    */

    $candidateName = trim(
        $data["candidateName"] ?? ""
    );

    $position = trim(
        $data["position"] ?? ""
    );

    $passportNumber = trim(
        $data["passportNumber"] ?? ""
    );

    $email = trim(
        $data["email"] ?? ""
    );

    $phone = trim(
        $data["phone"] ?? ""
    );

    $candidateDetails = trim(
        $data["candidateDetails"] ?? ""
    );


    /*
    |--------------------------------------------------------------------------
    | Required validation
    |--------------------------------------------------------------------------
    */

    if (
        empty($candidateName) ||
        empty($position) ||
        empty($passportNumber) ||
        empty($email) ||
        empty($phone)
    ) {

        echo json_encode([
            "success" => false,
            "message" =>
                "Please fill all required fields."
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Email validation
    |--------------------------------------------------------------------------
    */

    if (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        echo json_encode([
            "success" => false,
            "message" =>
                "Invalid email address."
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | CV validation
    |--------------------------------------------------------------------------
    */

    if (
        !isset($_FILES["cv"]) ||
        $_FILES["cv"]["error"] !== UPLOAD_ERR_OK
    ) {

        echo json_encode([
            "success" => false,
            "message" =>
                "Please attach your CV."
        ]);

        exit;
    }


    $cv = $_FILES["cv"];


    /*
    |--------------------------------------------------------------------------
    | CV size
    |--------------------------------------------------------------------------
    |
    | Maximum 5MB
    |
    */

    $maxSize =
        5 * 1024 * 1024;


    if ($cv["size"] > $maxSize) {

        echo json_encode([
            "success" => false,
            "message" =>
                "CV must be less than 5MB."
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | CV extension
    |--------------------------------------------------------------------------
    */

    $extension = strtolower(
        pathinfo(
            $cv["name"],
            PATHINFO_EXTENSION
        )
    );


    $allowedExtensions = [
        "pdf",
        "doc",
        "docx"
    ];


    if (
        !in_array(
            $extension,
            $allowedExtensions,
            true
        )
    ) {

        echo json_encode([
            "success" => false,
            "message" =>
                "Only PDF, DOC and DOCX files are allowed."
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Recipient
    |--------------------------------------------------------------------------
    */

    $to = "admin@skhrservices.in";


    /*
    |--------------------------------------------------------------------------
    | Subject
    |--------------------------------------------------------------------------
    */

    $subject =
        "New Candidate Application - " .
        $position;


    /*
    |--------------------------------------------------------------------------
    | Email body
    |--------------------------------------------------------------------------
    */

    $body = "";

    $body .= "NEW CANDIDATE APPLICATION";
    $body .= "\r\n";
    $body .= "=========================";
    $body .= "\r\n\r\n";

    $body .= "Candidate Name: ";
    $body .= $candidateName;
    $body .= "\r\n";

    $body .= "Position Applied For: ";
    $body .= $position;
    $body .= "\r\n";

    $body .= "Passport Number: ";
    $body .= $passportNumber;
    $body .= "\r\n";

    $body .= "Email: ";
    $body .= $email;
    $body .= "\r\n";

    $body .= "Contact Number: ";
    $body .= $phone;
    $body .= "\r\n\r\n";

    $body .= "Additional Details:";
    $body .= "\r\n";

    $body .= $candidateDetails;
    $body .= "\r\n";


    /*
    |--------------------------------------------------------------------------
    | File information
    |--------------------------------------------------------------------------
    */

    $fileName =
        basename($cv["name"]);

    $filePath =
        $cv["tmp_name"];


    /*
    |--------------------------------------------------------------------------
    | Read CV
    |--------------------------------------------------------------------------
    */

    $fileContent =
        file_get_contents($filePath);


    if ($fileContent === false) {

        echo json_encode([
            "success" => false,
            "message" =>
                "Unable to read CV file."
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Encode CV
    |--------------------------------------------------------------------------
    */

    $encodedFile =
        chunk_split(
            base64_encode(
                $fileContent
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Boundary
    |--------------------------------------------------------------------------
    */

    $boundary =
        md5(
            uniqid(
                time(),
                true
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Headers
    |--------------------------------------------------------------------------
    */

    $headers = "";

    $headers .=
        "From: website@printdeal.co.in\r\n";

    $headers .=
        "Reply-To: " . $email . "\r\n";

    $headers .=
        "MIME-Version: 1.0\r\n";

    $headers .=
        "Content-Type: multipart/mixed; boundary=\""
        . $boundary .
        "\"\r\n";


    /*
    |--------------------------------------------------------------------------
    | Message
    |--------------------------------------------------------------------------
    */

    $message = "";


    /*
    | Text section
    */

    $message .=
        "--" . $boundary . "\r\n";

    $message .=
        "Content-Type: text/plain; charset=UTF-8\r\n";

    $message .=
        "Content-Transfer-Encoding: 7bit\r\n\r\n";

    $message .=
        $body . "\r\n";


    /*
    | Attachment section
    */

    $message .=
        "--" . $boundary . "\r\n";

    $message .=
        "Content-Type: application/octet-stream; ";
    
    $message .=
        "name=\"" . $fileName . "\"\r\n";

    $message .=
        "Content-Disposition: attachment; ";
    
    $message .=
        "filename=\"" . $fileName . "\"\r\n";

    $message .=
        "Content-Transfer-Encoding: base64\r\n\r\n";

    $message .=
        $encodedFile . "\r\n";


    /*
    | End boundary
    */

    $message .=
        "--" . $boundary . "--\r\n";


    /*
    |--------------------------------------------------------------------------
    | Send candidate email
    |--------------------------------------------------------------------------
    */

    $sent = mail(
        $to,
        $subject,
        $message,
        $headers
    );


    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    if ($sent) {

        echo json_encode([
            "success" => true,
            "message" =>
                "Application submitted successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" =>
                "Unable to send application."
        ]);
    }

    exit;
}


/*
|--------------------------------------------------------------------------
| Invalid form
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => false,
    "message" => "Invalid form type."
]);

exit;

?>
