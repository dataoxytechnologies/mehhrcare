<?php
class TestAdmin extends Public_Controller {
    public function index()
    {
        $email = 'admin@1d1.co';
        $this->load->library('Auth');
        $admin = $this->auth->get_admin_by_email($email);
        echo $admin ? "Found admin with email $email" : "No admin found with email $email";
    }
}
