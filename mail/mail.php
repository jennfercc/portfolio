<?php
 
// EDIT THE 2 LINES BELOW AS REQUIRED
$email_to      = "your.email@mail.com";

// edit text content for form
$text_content = [
  'subject' => "Message from contact form",
  'messages' => [
    'error'   => 'There was an error sending, please try again later.',
    'success' => 'Your message has been sent successfully.',
    'validation' => [
      'name'    => 'Name is required.',
      'email'   => 'Email is invalid.',
      'subject' => 'Subject is required.',
      'message' => 'Message is required.'
    ]
  ]
];

$errors = array();
$data   = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
  $name       = stripslashes(trim($_POST['name']));     // required
  $email_from = stripslashes(trim($_POST['email']));    // required
  $subject    = stripslashes(trim($_POST['subject']));  // required
  $message    = stripslashes(trim($_POST['message']));  // required

  $error_message = "";
  $email_exp     = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
  $string_exp    = "/^[A-Za-z .'-]+$/";
 
  if(!preg_match($string_exp,$name)) {
    $errors['name'] = $text_content['messages']['validation']['name'];
  }
 
  if(!preg_match($email_exp,$email_from)) {
    $errors['email'] = $text_content['messages']['validation']['email'];
  }

  if (empty($subject)) {
    $errors['subject'] = $text_content['messages']['validation']['subject'];
  }

  if (empty($message)) {
    $errors['message'] = $text_content['messages']['validation']['message'];
  }
 
  if(strlen($error_message) > 0) {
    died($error_message);
  }

  if (!empty($errors)) {
    $data['success'] = false;
    $data['errors']  = $errors;
  } else {

    $email_message = "Form details below.\n\n";
 
    function clean_string($string) {
      $bad = array("content-type","bcc:","to:","cc:","href");
      return str_replace($bad,"",$string);
    }

    $email_message .= "Name: "   .clean_string($name)."\n";
    $email_message .= "Email: "  .clean_string($email_from)."\n";
    $email_message .= "subject: ".clean_string($subject)."\n";
    $email_message .= "Message: ".clean_string($message)."\n";

    // create email headers
    $headers = 'From: '.$email_from."\r\n". 
    'Reply-To: '.$email_from."\r\n" . 
    'X-Mailer: PHP/' . phpversion();
     
    @mail($email_to, $text_content['subject'], $email_message, $headers);

    $data['success'] = true;
    $data['message'] = $text_content['messages']['success'];
  }

  echo json_encode($data);
}