<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ListPermintaan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        is_login();
    }

    public function index() {
        //$this->load->view('table');
        $this->template->load('template', 'list_permintaan/index');
    }
}
