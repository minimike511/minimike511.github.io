<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 2016-05-15
 * Time: 오후 10:11
 */

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    header("HTTP/1.1 403 Forbidden");
    exit;
}

require '../../vendor/autoload.php';
$getPost = (array)json_decode(file_get_contents('php://input'));

$sendgrid = new SendGrid($_ENV["SENDGRID_API_KEY"]);

$email = new \SendGrid\Mail\Mail();

$email->addTo($getPost['sendTo']);
$email->setFrom($getPost['sendFrom']);
$email->setSubject($getPost['subject']);
$email->addContent("text/plain", $getPost['msg']);
$email->addContent("text/html", $getPost['msgHTML']);

try {
    $sendgrid->send($email);
    echo json_encode(array('success' => true, 'message' => "done"));
} catch (\SendGrid\Exception $e) {
    $err = $e->getCode() . "\n";
    foreach ($e->getErrors() as $er) {
        $err = $err . $er . "\n";
    }
    echo json_encode(array('success' => false, 'message' => $err));
}
