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
            if ( ! empty($_FILES))
            {
                // ファイルは複数ではない
                if (count($_FILES['file']['name']) > 1)
                {
                    // error処理
                    throw new RuntimeException('ファイルが複数設定されています。');
                }

                // エラーがない
                switch ($_FILES['file']['error'])
                {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new RuntimeException('ファイルのサイズが大きるためアップロードされませんでした。');
                    case UPLOAD_ERR_PARTIAL:
                        throw new RuntimeException('ファイルは完全にアップロードされませんでした。');
                    case UPLOAD_ERR_NO_FILE:
                        throw new RuntimeException('ファイルはアップロードされませんでした。');
                    case UPLOAD_ERR_NO_TMP_DIR:
                        throw new RuntimeException('テンポラリフォルダがありません。');
                    case UPLOAD_ERR_CANT_WRITE:
                        throw new RuntimeException('ディスクへの書き込みに失敗しました。');
                    case UPLOAD_ERR_EXTENSION:
                        throw new RuntimeException('異常が発生したためアップロードに失敗しました。');
                }

                // MIMEチェック
                $mime_type = mime_content_type($_FILES['file']['tmp_name']);
                $match_result = preg_match('/^image\/(jpeg|png)$/', $mime_type);
                if ($match_result === 0 || $match_result === false)
                {
                    throw new RuntimeException('許可されていないファイル形式です。');
                }

                $uploaddir = APPPATH . '../public/uploads/';
                $uploadfile = $uploaddir . basename($_FILES['file']['name']);

                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile) === false) {
                    throw new RuntimeException('ファイルのアップロードに失敗しました。');
                }

                chmod($uploadfile, 0744);
            }
            else
            {
                throw new RuntimeException('ファイルが設定されていません');
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