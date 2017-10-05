<?php

class Backend_Controller extends CI_Controller {

    public function __construct()
	{
        parent::__construct();

        $this->output->enable_profiler(TRUE);

        // 残っていればsessionの有効期限が更新されます
        session_start();
        if (is_null($_SESSION['user_id']))
        {
            redirect('backend/login');
        }
    }
}