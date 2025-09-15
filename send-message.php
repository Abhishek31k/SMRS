<?php
if (isset($_POST['send'])) {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // College email where messages will be delivered
    $to = "info@crrce.edu.in";  

    // Email subject
    $mail_subject = "New Contact Form Message: " . $subject;

    // Email content
    $body = "
    You have received a new message from the Contact Form at Sir CRR College of Engineering.
    
    Name: $name
    Email: $email
    Subject: $subject
    
    Message:
    $message
    ";

    // Email headers
    $headers  = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Send Email
    if (mail($to, $mail_subject, $body, $headers)) {
        echo "<script>alert('Thank you! Your message has been sent successfully.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Sorry! Something went wrong, please try again later.'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
