<?php

class Cron_Job_Schedule implements plugin_cronjob_module
{
    public function init()
    {
        add_filter('cron_schedules', array($this, 'add_fifteen_min_cron_schedule'));
    }

    public function add_fifteen_min_cron_schedule($schedules)
    {
        $schedules['fifteen_minutes'] = array(
            'interval' => 15 * 60,
            'display'  => __('Every 15 Minutes')
        );
        return $schedules;
    }
}
