<?php

class Logout extends Backend_Controller {

    /**
     * ログアウト
     *
     * @return void
     */
    public function index()
    {
        $this->load->model('service/User_backend');
        $this->User_backend->sign_out();
        redirect('backend/login');
    }
}