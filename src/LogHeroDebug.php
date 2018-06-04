<?php
namespace LogHero\Client;
require_once __DIR__ . '/LogEvent.php';

class DebugLogEvent extends LogEvent  {

    public function columns() {
        $columns = parent::columns();
        array_push($columns, 'rawIp');
        return $columns;
    }

    public function row() {
        $rows = parent::row();
        array_push($rows, $this->ipAddress);
        return $rows;
    }

}
