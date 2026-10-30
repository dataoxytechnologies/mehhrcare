<?php
class AdminCheck extends Public_Controller {
    public function __construct()
    {
        $this->load->database();
    }
    public function index()
    {
        $rows = $this->db->get('admin')->result_array();
        echo count($rows) . " admin rows found.<br>";
        foreach ($rows as $row) {
            echo $row['email'] . "<br>";
        }
    }
}
