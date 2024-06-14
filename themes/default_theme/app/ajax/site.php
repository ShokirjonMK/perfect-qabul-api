<?php

$output = json_output();
$action = input_post('post_action');

if ($action == 'contact_form') {
    $run = true;
    $message = _e('Incorrect email address or password, please try again');

    $fullname = input_post('fullname');
    $email = input_post('email');
    $phone = input_post('phone');
    $subject = input_post('subject');
    $msg = input_post('message');

    if (empty($email)) {
        $run = false;
        $message = _e('Please enter your email address');
    } elseif (empty($email)) {
        $run = false;
        $message = _e('Please enter your email address');
    } elseif (!is_email($email)) {
        $run = false;
        $message = _e('Please enter correct email address');
    } elseif (empty($phone)) {
        $run = false;
        $message = _e('Please enter your phone number');
    } elseif (empty($msg)) {
        $run = false;
        $message = _e('Please enter the message');
    }

    if ($run) {
        $view = ['html' => '@frontend/mail/contact/contact-message'];
        $data = [
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $msg,
        ];

        $to = get_param('infoEmail');
        $subject = _e('Message: {subject}', ['subject' => $subject]);

        send_mail($to, $subject, $view, $data);

        $error = false;
        $success = true;
        $message = _e('Your message has been sent. We will get back to you as soon as possible.');
    }

    if ($success) {
        $output = json_output('success');
        $output['redirect'] = false;
        $output['message'] = $message;
    }
}

return $output;
