<?php

class Database_Model extends CI_Model {

    public $table_name;

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $target_db = $this->cache->memcached->get('target_db');
        if ($target_db !== false)
        {
            $this->db->db_select($target_db);
        }

        $this->table_name = lcfirst(get_class($this));
    }
}