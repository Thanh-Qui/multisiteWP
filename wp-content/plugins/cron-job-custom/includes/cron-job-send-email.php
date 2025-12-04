<?php

function my_send_5_minutes_email_func() {
    $to      = 'quiluong111@gmail.com';
    $subject = 'Email gửi 5 phút 1 lần';
    $message = 'Hello Hello mọi người';
    
    $mail_sent = wp_mail( $to, $subject, $message );
    
    if ( $mail_sent ) {
        error_log( 'Cron Email 5 phút đã được gửi thành công: ' . date('Y-m-d H:i:s') );
    } else {
        error_log( 'Cron Email 5 phút KHÔNG thể gửi. Vui lòng kiểm tra cấu hình SMTP/gửi mail.' );
    }
}
add_action('send_5_minutes_email', 'my_send_5_minutes_email_func');

