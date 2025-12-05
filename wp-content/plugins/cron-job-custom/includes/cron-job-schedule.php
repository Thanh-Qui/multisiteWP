<?php

function add_fifteen_min_cron_schedule( $schedules ) {
    $schedules['fifteen_minutes'] = array(
        'interval' => 15 * 60,
        'display'  => __( 'Every 15 Minutes' )
    );
    return $schedules;
}