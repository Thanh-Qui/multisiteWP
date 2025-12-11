<?php

class send_email_facade {
    public static function send_mail($to, $subject, $message) {

        // create headers for html email
        $headers =  array('Content-Type: text/html; charset=UTF-8');

        $is_send = wp_mail($to, $subject, $message, $headers);

        if ($is_send) {
            error_log("Successfully sent email");
        }else {
            error_log("Failed to send email");
        }

        return $is_send;
    }
}

class Cron_Job_Send_Email implements plugin_cronjob_module
{
    public function init()
    {
        add_action('send_5_minutes_email', array($this, 'send_5_minutes_email'));
    }

    public function send_5_minutes_email()
    {
        $to      = 'quiluong111@gmail.com';
        $subject = 'Email gửi 5 phút 1 lần';
        $message = 'Hello Hello mọi người';

        send_email_facade::send_mail($to, $subject, $message);
    }
}
