<?php

/**
 * @see http://codeigniter.jp/user_guide/3/database/query_builder.html
 * @see http://codeigniter.jp/user_guide/3/database/results.html
 */
class Backend_user extends Database_Model {

    /**
     * 管理者を取得
     *
     * @return object|null
     */
    public function get_admin()
	{
        $this->load->model('service/Role_backend');

        // object|null
        $result = $this->db
            ->where('role', $this->Role_backend::ADMIN)
            ->where('delete_flag', 0)
            ->get($this->table_name)
            ->row();

        return $result;
    }

    /**
     * ユーザー作成
     *
     * @param array $user_data
     * @return boolean
     * @throws Exception
     */
    public function create(array $user_data): bool
	{
        // 同名ユーザー
        $user_object = $this->get_by_name($user_data['name']);

        if (isset($user_object))
        {
            throw new Exception('そのユーザー名は既に使用されています。');
        }

        $this->db->insert($this->table_name, $user_data);
        $result = $this->db->affected_rows();

        return $result === 1;
    }

    /**
     * ユーザー名で1つのみ取得
     *
     * @param string $name
     * @return object|null
     */
    public function get_by_name(string $name)
	{
        // object|null
        $result = $this->db
            ->where('name', $name)
            ->where('delete_flag', 0)
            ->get($this->table_name)
            ->row();

        return $result;
    }

    /**
     * ユーザーIDで取得
     *
     * @param int $id
     * @return object|null
     */
    public function get_by_id(int $id)
	{
        // object|null
        $result = $this->db
            ->where('id', $id)
            ->where('delete_flag', 0)
            ->get($this->table_name)
            ->row();

        return $result;
    }

    /**
     * 特定権限以外のユーザーの取得
     *
     * @param []int $role_list
     * @return []object
     */
    public function get_list_by_ignore_role(array $role_list): array
	{
        // Object[]
        $result = $this->db
            ->where_not_in('role', $role_list)
            ->where('delete_flag', 0)
            ->get($this->table_name)
            ->result();

        return $result;
    }
}