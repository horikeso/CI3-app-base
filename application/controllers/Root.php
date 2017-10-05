<?php

class Root extends CI_Controller {

    public function index()
    {
        $this->output->enable_profiler(TRUE);
        $this->smarty->view('index.html');
    }
}