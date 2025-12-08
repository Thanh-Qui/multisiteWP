<?php

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

        $mail_sent = wp_mail($to, $subject, $message);

        if ($mail_sent) {
            error_log('Cron Email 5 phút đã được gửi thành công: ' . date('Y-m-d H:i:s'));
        } else {
            error_log('Cron Email 5 phút KHÔNG thể gửi. Vui lòng kiểm tra cấu hình SMTP/gửi mail.');
        }
    }
}
