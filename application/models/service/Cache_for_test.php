<?php

/**
 * テストからmemchachedを使用するためのmodel
 */
class Cache_for_test extends CI_Model {

    /**
     * 保存
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function save(string $key, $value)
	{
        $this->cache->memcached->save($key, $value);
    }

    /**
     * 取得
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
	{
        return $this->cache->memcached->get($key);
    }

    /**
     * 削除
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key)
	{
        $this->cache->memcached->delete($key);
    }
}