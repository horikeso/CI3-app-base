<?php

class Root extends CI_Controller {

    public function index()
    {
        $data = [];
        $data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        $this->smarty->view('index.html', $data);
    }

    public function execute_upload()
    {
        $data = [];
        $data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        try
        {
            // アップロード設定
            $config['upload_path'] = APPPATH . '../public/uploads/';
            $config['allowed_types'] = 'jpg|png';
            $config['file_name'] = 'images';// 拡張子を省略すると元々の拡張子で保存してくれます。
            $config['overwrite'] = true;

            $this->load->library('upload', $config);

            // アップロード
            if ( ! $this->upload->do_upload('file'))
            {
                throw new RuntimeException($this->upload->display_errors());
            }
        }
        catch (RuntimeException $exception)
        {
            log_message('error', $exception);
            set_status_header(500);
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }
}