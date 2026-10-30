<?php
class TableList extends Public_Controller
{
    public function index()
    {
        $tables = $this->db->list_tables();
        echo implode('<br>', $tables);
    }
}
