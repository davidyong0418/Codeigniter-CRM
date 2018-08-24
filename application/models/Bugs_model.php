<?php

class Bugs_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function get_time_spent_result($seconds) {
        $minutes = $seconds / 60;
        $hours = $minutes / 60;
        if ($minutes >= 60) {
            return round($hours, 2) . ' ' . lang('hours');
        } elseif ($seconds > 60) {
            return round($minutes, 2) . ' ' . lang('minutes');
        } else {
            return $seconds . ' ' . lang('seconds');
        }
    }

}
