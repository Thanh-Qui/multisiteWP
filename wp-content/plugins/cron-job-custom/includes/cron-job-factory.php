<?php

class Cron_job_factory {
    public static function create($type) {
        switch ($type) {
            case 'send_email':
                return new Cron_Job_Send_Email();
            case 'email_product':
                return new Cron_Job_Email_Product();
            case 'schedule':
                return new Cron_Job_Schedule();
            default:
                throw new Exception("Unknown cron job type: " . $type);
        }
    }
}