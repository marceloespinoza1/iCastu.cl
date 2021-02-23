<?php

namespace ElfsightContactFormApi;


class Records {
    private $Helper;

    private $tableName;

    public function __construct($Helper) {
        $this->Helper = $Helper;

        $this->tableName = $this->Helper->getTableName('mails');

        if (!$this->Helper->tableExist($this->tableName)) {
            $this->Helper->tableCreate($this->tableName, array('public_id' => 'varchar(255)', 'data' => 'text', 'widget_id' => 'int(11)'));
        }
    }

    public function insert($data){
        global $wpdb;

        $wpdb->insert($this->tableName, $data);
    }
}
