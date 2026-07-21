<?php

$to = "info@beautybop.co.uk";
$subject = "BeautyBop PHP Mail Test";
$message = "If you receive this, PHP mail() is working.";
$headers = "From: info@beautybop.co.uk\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Mail sent successfully";
} else {
    echo "Mail failed";
}