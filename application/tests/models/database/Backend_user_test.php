<?php

/**
 * @group database
 *
 * MonkeyPatch https://github.com/kenjis/ci-phpunit-test/blob/master/docs/FunctionAndClassReference.md
 */
class Backend_user_test extends TestCase
{
    private static $target_db;
    private static $table_name;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$target_db = 'app_test';
        self::$table_name = 'backend_user';
    }

    public function setUp()
    {
        $this->resetInstance();

        $this->CI->load->model('service/Cache_for_test');
        $this->CI->Cache_for_test->save('target_db', self::$target_db);

        $this->CI->load->model('database/Backend_user');
    }

    protected function tearDown()
    {
        $this->CI->db->truncate(self::$table_name);

        $this->CI->Cache_for_test->delete('target_db');

        MonkeyPatch::resetFunctions();
        MonkeyPatch::resetConstants();

        parent::tearDown();
    }

    public function test_get_admin_select()
    {
        $user_data = [
            'display_id' => 'display_id',
            'name' => 'name',
            'password' => 'password',
            'role' => 1,
            'mailadress' => 'mailadress',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $this->CI->db->insert(self::$table_name, $user_data);

        $expected = new stdClass();
        $expected->id = '1';
        $expected->display_id = $user_data['display_id'];
        $expected->name = $user_data['name'];
        $expected->password = $user_data['password'];
        $expected->role = (string)$user_data['role'];
        $expected->mailadress = $user_data['mailadress'];
        $expected->update_datetime = $user_data['update_datetime'];
        $expected->create_datetime = $user_data['create_datetime'];
        $expected->delete_datetime = $user_data['delete_datetime'];
        $expected->delete_flag = (string)$user_data['delete_flag'];

        $this->assertEquals($expected, $this->CI->Backend_user->get_admin());
    }

    public function test_get_admin_no_select()
    {
        $user_data1 = [
            'display_id' => 'display_id1',
            'name' => 'name1',
            'password' => 'password1',
            'role' => 1,
            'mailadress' => 'mailadress1',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => '2017-10-10 00:00:00',
            'delete_flag' => 1,
        ];

        $user_data2 = [
            'display_id' => 'display_id2',
            'name' => 'name2',
            'password' => 'password2',
            'role' => 2,
            'mailadress' => 'mailadress2',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $this->CI->db->insert(self::$table_name, $user_data1);
        $this->CI->db->insert(self::$table_name, $user_data2);

        $this->assertNull($this->CI->Backend_user->get_admin());
    }

    public function test_create_success()
    {
        $user_data = [
            'display_id' => 'display_id',
            'name' => 'name',
            'password' => 'password',
            'role' => 2,
            'mailadress' => 'mailadress',
        ];

        $expected = true;
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data));
        $this->assertSame(1, $this->CI->db->count_all(self::$table_name));
    }

    public function test_create_failure()
    {
        $user_data = [
            'display_id' => 'display_id',
            'name' => 'name',
            'password' => null,
            'role' => null,
            'mailadress' => 'mailadress',
        ];

        $expected = false;
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage そのユーザー名は既に使用されています。
     */
    public function test_create_exception()
    {
        $user_data = [
            'display_id' => 'display_id',
            'name' => 'name',
            'password' => 'password',
            'role' => 2,
            'mailadress' => 'mailadress',
        ];

        $expected = true;
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data));
        $this->CI->Backend_user->create($user_data);
    }

    public function test_get_by_name_select()
    {
        $user_data = [
            'display_id' => 'display_id',
            'name' => 'name',
            'password' => 'password',
            'role' => 2,
            'mailadress' => 'mailadress',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $this->CI->db->insert(self::$table_name, $user_data);

        $expected = new stdClass();
        $expected->id = '1';
        $expected->display_id = $user_data['display_id'];
        $expected->name = $user_data['name'];
        $expected->password = $user_data['password'];
        $expected->role = (string)$user_data['role'];
        $expected->mailadress = $user_data['mailadress'];
        $expected->update_datetime = $user_data['update_datetime'];
        $expected->create_datetime = $user_data['create_datetime'];
        $expected->delete_datetime = $user_data['delete_datetime'];
        $expected->delete_flag = (string)$user_data['delete_flag'];

        $this->assertEquals($expected, $this->CI->Backend_user->get_by_name($user_data['name']));
    }

    public function test_get_by_name_no_select()
    {
        $user_data = [
            'display_id' => 'display_id',
            'name' => 'name',
            'password' => 'password',
            'role' => 2,
            'mailadress' => 'mailadress',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => '2017-10-10 00:00:00',
            'delete_flag' => 1,
        ];

        $this->CI->db->insert(self::$table_name, $user_data);

        $this->assertNull($this->CI->Backend_user->get_by_name('name'));
    }

    public function test_get_by_id_select()
    {
        $user_data = [
            'display_id' => 'display_id',
            'name' => 'name',
            'password' => 'password',
            'role' => 2,
            'mailadress' => 'mailadress',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $this->CI->db->insert(self::$table_name, $user_data);

        $expected = new stdClass();
        $expected->id = '1';
        $expected->display_id = $user_data['display_id'];
        $expected->name = $user_data['name'];
        $expected->password = $user_data['password'];
        $expected->role = (string)$user_data['role'];
        $expected->mailadress = $user_data['mailadress'];
        $expected->update_datetime = $user_data['update_datetime'];
        $expected->create_datetime = $user_data['create_datetime'];
        $expected->delete_datetime = $user_data['delete_datetime'];
        $expected->delete_flag = (string)$user_data['delete_flag'];

        $this->assertEquals($expected, $this->CI->Backend_user->get_by_id(1));
    }

    public function test_get_by_id_no_select()
    {
        $user_data = [
            'display_id' => 'display_id',
            'name' => 'name',
            'password' => 'password',
            'role' => 2,
            'mailadress' => 'mailadress',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => '2017-10-10 00:00:00',
            'delete_flag' => 1,
        ];

        $this->CI->db->insert(self::$table_name, $user_data);

        $this->assertNull($this->CI->Backend_user->get_by_id(1));
    }

    public function test_get_list_by_ignore_role_select()
    {
        $user_data1 = [
            'display_id' => 'display_id1',
            'name' => 'name1',
            'password' => 'password1',
            'role' => 1,
            'mailadress' => 'mailadress1',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $user_data2 = [
            'display_id' => 'display_id2',
            'name' => 'name2',
            'password' => 'password2',
            'role' => 1,
            'mailadress' => 'mailadress2',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $user_data3 = [
            'display_id' => 'display_id3',
            'name' => 'name3',
            'password' => 'password3',
            'role' => 2,
            'mailadress' => 'mailadress3',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $user_data4 = [
            'display_id' => 'display_id4',
            'name' => 'name4',
            'password' => 'password4',
            'role' => 3,
            'mailadress' => 'mailadress4',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => '2017-10-10 00:00:00',
            'delete_flag' => 1,
        ];

        $user_data5 = [
            'display_id' => 'display_id5',
            'name' => 'name5',
            'password' => 'password5',
            'role' => 3,
            'mailadress' => 'mailadress5',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $expected = true;
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data1));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data2));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data3));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data4));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data5));

        $object1 = new stdClass();
        $object1->id = '3';
        $object1->display_id = $user_data3['display_id'];
        $object1->name = $user_data3['name'];
        $object1->password = $user_data3['password'];
        $object1->role = (string)$user_data3['role'];
        $object1->mailadress = $user_data3['mailadress'];
        $object1->update_datetime = $user_data3['update_datetime'];
        $object1->create_datetime = $user_data3['create_datetime'];
        $object1->delete_datetime = $user_data3['delete_datetime'];
        $object1->delete_flag = (string)$user_data3['delete_flag'];

        $object2 = new stdClass();
        $object2->id = '5';
        $object2->display_id = $user_data5['display_id'];
        $object2->name = $user_data5['name'];
        $object2->password = $user_data5['password'];
        $object2->role = (string)$user_data5['role'];
        $object2->mailadress = $user_data5['mailadress'];
        $object2->update_datetime = $user_data5['update_datetime'];
        $object2->create_datetime = $user_data5['create_datetime'];
        $object2->delete_datetime = $user_data5['delete_datetime'];
        $object2->delete_flag = (string)$user_data5['delete_flag'];

        $expected = [$object1, $object2];

        $this->assertEquals($expected, $this->CI->Backend_user->get_list_by_ignore_role([1]));
    }

    public function test_get_list_by_ignore_role_select_multi_role()
    {
        $user_data1 = [
            'display_id' => 'display_id1',
            'name' => 'name1',
            'password' => 'password1',
            'role' => 1,
            'mailadress' => 'mailadress1',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $user_data2 = [
            'display_id' => 'display_id2',
            'name' => 'name2',
            'password' => 'password2',
            'role' => 1,
            'mailadress' => 'mailadress2',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $user_data3 = [
            'display_id' => 'display_id3',
            'name' => 'name3',
            'password' => 'password3',
            'role' => 2,
            'mailadress' => 'mailadress3',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $user_data4 = [
            'display_id' => 'display_id4',
            'name' => 'name4',
            'password' => 'password4',
            'role' => 3,
            'mailadress' => 'mailadress4',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => '2017-10-10 00:00:00',
            'delete_flag' => 1,
        ];

        $user_data5 = [
            'display_id' => 'display_id5',
            'name' => 'name5',
            'password' => 'password5',
            'role' => 3,
            'mailadress' => 'mailadress5',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $expected = true;
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data1));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data2));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data3));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data4));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data5));

        $object1 = new stdClass();
        $object1->id = '5';
        $object1->display_id = $user_data5['display_id'];
        $object1->name = $user_data5['name'];
        $object1->password = $user_data5['password'];
        $object1->role = (string)$user_data5['role'];
        $object1->mailadress = $user_data5['mailadress'];
        $object1->update_datetime = $user_data5['update_datetime'];
        $object1->create_datetime = $user_data5['create_datetime'];
        $object1->delete_datetime = $user_data5['delete_datetime'];
        $object1->delete_flag = (string)$user_data5['delete_flag'];

        $expected = [$object1];

        $this->assertEquals($expected, $this->CI->Backend_user->get_list_by_ignore_role([1, 2]));
    }

    public function test_get_list_by_ignore_role_no_select()
    {
        $user_data1 = [
            'display_id' => 'display_id1',
            'name' => 'name1',
            'password' => 'password1',
            'role' => 1,
            'mailadress' => 'mailadress1',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $user_data2 = [
            'display_id' => 'display_id2',
            'name' => 'name2',
            'password' => 'password2',
            'role' => 1,
            'mailadress' => 'mailadress2',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => null,
            'delete_flag' => 0,
        ];

        $user_data3 = [
            'display_id' => 'display_id3',
            'name' => 'name3',
            'password' => 'password3',
            'role' => 2,
            'mailadress' => 'mailadress3',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => '2017-10-10 00:00:00',
            'delete_flag' => 1,
        ];

        $user_data4 = [
            'display_id' => 'display_id4',
            'name' => 'name4',
            'password' => 'password4',
            'role' => 3,
            'mailadress' => 'mailadress4',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => '2017-10-10 00:00:00',
            'delete_flag' => 1,
        ];

        $user_data5 = [
            'display_id' => 'display_id5',
            'name' => 'name5',
            'password' => 'password5',
            'role' => 3,
            'mailadress' => 'mailadress5',
            'update_datetime' => '2017-10-10 00:00:00',
            'create_datetime' => '2017-10-10 00:00:00',
            'delete_datetime' => '2017-10-10 00:00:00',
            'delete_flag' => 1,
        ];

        $expected = true;
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data1));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data2));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data3));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data4));
        $this->assertSame($expected, $this->CI->Backend_user->create($user_data5));

        $expected = [];

        $this->assertEquals($expected, $this->CI->Backend_user->get_list_by_ignore_role([1]));
    }

}
