<?php

class User extends Backend_api_Controller {

    /**
     * API ユーザー
     *
     * @return void
     */
    public function index()
    {
        if ($this->input->method() === 'get')
        {
            return $this->get();
        }
    }

    private function get()
    {
        $start = $this->input->get('page');
        $unit = $this->input->get('unit');

        if (is_null($start) && is_null($unit))
        {
            $start = '1';
            $unit = '10';
        }

        $this->load->model('service/User_backend');
        $user_object_list = $this->User_backend->get_user_list_without_admin_by_offset($start, $unit);

        // json
        header('Content-Type: application/json');
        echo json_encode($user_object_list);
    }
}