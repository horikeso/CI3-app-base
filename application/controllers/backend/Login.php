<?php

class Login extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->output->enable_profiler(TRUE);

        session_start();
        if (isset($_SESSION['user_id']))
        {
            redirect('backend/top');
        }
    }

    /**
     * ログイン画面
     *
     * @return void
     */
    public function index()
    {
        $data = [];
        $data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        $this->load->model('service/User_backend');
        if ($this->User_backend->is_initial_setting() === true)
        {
            // 管理者ユーザー登録画面に遷移
            redirect('backend/login/initialize');
            return;
        };

        if (is_null($this->input->post()))
        {
            $this->smarty->view('backend/login/index.html', $data);
            return;
        }

        $data['name'] = $this->input->post('name', true);
        $data['password'] = $this->input->post('password', true);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'ユーザー名', 'required|max_length[20]');
        $this->form_validation->set_rules('password', 'パスワード', 'required|min_length[6]');

        if ($this->form_validation->run() === true) {
            // バリデーションエラーがない場合にログイン

            $user_data = [
                'name' => $this->input->post('name', true),
                'password' => $this->input->post('password', true),
            ];

            try
            {
                 $result = $this->User_backend->sign_in($user_data);
                 if ($result === false)
                 {
                    $data['error_message'] = 'ログインに失敗しました。';
                    $this->smarty->view('backend/login/index.html', $data);
                    return;
                 }
            }
            catch (Exception $exception)
            {
                $data['error_message'] = $exception->getMessage();
                $this->smarty->view('backend/login/index.html', $data);
                return;
            }

            // 管理画面に遷移
            redirect('backend/top');
            return;
        }

        // バリデーションエラーの場合
        $data['validation_errors'] = validation_errors();
        $this->smarty->view('backend/login/index.html', $data);
        return;
    }

    /**
     * 管理者ユーザー登録画面
     *
     * @return void
     */
    public function initialize()
    {
        $data = [];
        $data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        $this->load->model('service/User_backend');
        if ($this->User_backend->is_initial_setting() === false)
        {
            // ログイン画面に遷移
            redirect('backend/login');
            return;
        }

        if (is_null($this->input->post()))
        {
            $this->smarty->view('backend/login/initialize.html', $data);
            return;
        }

        $data['name'] = $this->input->post('name', true);
        $data['mailadress'] = $this->input->post('mailadress', true);
        $data['password'] = $this->input->post('password', true);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'ユーザー名', 'required|max_length[20]');
        $this->form_validation->set_rules('mailadress', 'メールアドレス', 'required|valid_email');
        $this->form_validation->set_rules('password', 'パスワード', 'required|min_length[6]');

        if ($this->form_validation->run() === true) {
            // バリデーションエラーがない場合に管理者ユーザーを登録

            $this->load->model('service/Role_backend');

            $user_data = [
                'name' => $this->input->post('name', true),
                'mailadress' => $this->input->post('mailadress', true),
                'password' => $this->input->post('password', true),
                'role' => $this->Role_backend::ADMIN,
            ];

            try
            {
                 $result = $this->User_backend->sign_up($user_data);
                 if ($result === false)
                 {
                    $data['error_message'] = '管理者ユーザーの登録に失敗しました。';
                    $this->smarty->view('backend/login/initialize.html', $data);
                    return;
                 }
            }
            catch (Exception $exception)
            {
                $data['error_message'] = $exception->getMessage();
                $this->smarty->view('backend/login/initialize.html', $data);
                return;
            }

            // ログイン画面に遷移
            redirect('backend/login');
            return;
        }

        // バリデーションエラーの場合
        $data['validation_errors'] = validation_errors();
        $this->smarty->view('backend/login/initialize.html', $data);
        return;
    }
}