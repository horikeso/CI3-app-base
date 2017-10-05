<?php

class User_backend extends CI_Model {

    /**
     * 初期設定が必要か
     *
     * @return boolean
     */
	public function is_initial_setting(): bool
	{
        $this->load->model('database/Backend_user');
        $user_object = $this->Backend_user->get_admin();
        return $user_object === null;
    }

    /**
     * ユーザー登録
     *
     * @param array $user_data
     * @return boolean
     * @throws Exception
     */
    public function sign_up(array $user_data): bool
	{
        $user_data['password'] = password_hash($user_data['password'], PASSWORD_BCRYPT);
        $user_data['display_id'] = uniqid().(string)rand(10000, 99999);
        $this->load->model('database/Backend_user');

        return $this->Backend_user->create($user_data);
	}

    /**
     * ログイン
     *
     * @param array $user_data
     * @return boolean
     */
	public function sign_in(array $user_data): bool
	{
        $this->load->model('database/Backend_user');
        $user_object = $this->Backend_user->get_by_name($user_data['name']);
        if (is_null($user_object))
        {
            return false;
        }

        $result = password_verify($user_data['password'], $user_object->password);
        if ($result === true && isset($_SESSION))
        {
            $_SESSION['user_id'] = (int)$user_object->id;
            $_SESSION['display_id'] = $user_object->display_id;
            session_regenerate_id(true);// ログイン後にセッションIDの変更（セキュリティ対策）
        }

        return $result;
    }

    /**
     * ログアウト
     *
     * @return void
     */
    public function sign_out()
	{
        $_SESSION = [];
        session_destroy();
    }

    /**
     * ユーザーの取得
     *
     * @param int $user_id
     * @return object|null
     */
	public function get_user(int $user_id)
	{
        $this->load->model('database/Backend_user');
        $user_object = $this->Backend_user->get_by_id($user_id);
        return $user_object;
    }

    /**
     * 全ての管理者以外のユーザーの取得
     *
     * @return []object
     */
    public function get_user_list_without_admin(): array
	{
        $this->load->model('service/Role_backend');
        $this->load->model('database/Backend_user');
        $user_object_list = $this->Backend_user->get_list_by_ignore_role([$this->Role_backend::ADMIN]);
        return $user_object_list;
    }

    /**
     * 全ての管理者以外のユーザーを範囲で取得（pagenation用）
     *
     * @param string|null $start
     * @param string|null $unit
     * @return []object
     */
    public function get_user_list_without_admin_by_offset(string $start = null, string $unit = null): array
	{
        $user_object_list = $this->get_user_list_without_admin();

        if (is_numeric($start) && is_numeric($unit) && (int)$start > 0 && (int)$unit > 0 && count($user_object_list) > 0)
        {
            // 取得する範囲を調整する
            $user_object_list_list = array_chunk($user_object_list, (int)$unit);
            if (count($user_object_list_list) >= (int)$start)
            {
                $user_object_list = $user_object_list_list[(int)$start - 1];
            }
            else
            {
                $user_object_list = $user_object_list_list[count($user_object_list_list)-1];
            }
        }

        return $user_object_list;
    }
}