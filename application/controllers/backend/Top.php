<?php

class Top extends Backend_Controller {

    /**
     * トップ画面
     *
     * @return void
     */
    public function index()
    {
        $data = [];
        $data['id'] = $_SESSION['display_id'];

        $this->load->model('service/User_backend');
        $user_object = $this->User_backend->get_user($_SESSION['user_id']);

        if (is_null($user_object))
        {
            redirect('backend/logout');
        }

        $this->load->model('service/Role_backend');
        if ((int)$user_object->role === $this->Role_backend::ADMIN)
        {
            $this->smarty->view('backend/top/admin_index.html', $data);
            return;
        }
    }
}