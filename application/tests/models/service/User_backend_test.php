<?php

/**
 * @group service
 *
 * MonkeyPatch https://github.com/kenjis/ci-phpunit-test/blob/master/docs/FunctionAndClassReference.md
 */
class User_backend_test extends TestCase
{
    // sign_outはテストしない（確認のしようがない）

    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model('service/User_backend');
    }

    protected function tearDown()
    {
        MonkeyPatch::resetFunctions();
        MonkeyPatch::resetConstants();

        parent::tearDown();
    }

    public function test_is_initial_setting_true()
    {
        MonkeyPatch::patchMethod(
            'Backend_user',
            ['get_admin' => null]
        );
        MonkeyPatch::verifyInvokedOnce(
            'Backend_user::get_admin', []
        );

        $expected = true;
        $this->assertSame($expected, $this->CI->User_backend->is_initial_setting());
    }

    public function test_is_initial_setting_false()
    {
        $result = new stdClass();

        MonkeyPatch::patchMethod(
            'Backend_user',
            ['get_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'Backend_user::get_admin', []
        );

        $expected = false;
        $this->assertSame($expected, $this->CI->User_backend->is_initial_setting());
    }

    public function test_sign_up_true()
	{
        $user_data = [];
        $user_data['password'] = 'test';

        // password_hashはモック出来ないようなので引数が固定できないのでverifyInvokedOnceは行わない
        // Can't patch on 'password_hash'
        /*
        MonkeyPatch::patchFunction('password_hash', 'hash_string', 'User_backend');
        MonkeyPatch::patchFunction('uniqid', 'abcd', 'User_backend');
        MonkeyPatch::patchFunction('rand', 10000, 'User_backend');
        */
        MonkeyPatch::patchMethod(
            'Backend_user',
            ['create' => true]
        );

        $expected = true;
        $this->assertSame($expected, $this->CI->User_backend->sign_up($user_data));
    }

    public function test_sign_up_false()
	{
        $user_data = [];
        $user_data['password'] = 'test';

        // password_hashはモック出来ないようなので引数が固定できないのでverifyInvokedOnceは行わない
        // Can't patch on 'password_hash'
        /*
        MonkeyPatch::patchFunction('password_hash', 'hash_string', 'User_backend');
        MonkeyPatch::patchFunction('uniqid', 'abcd', 'User_backend');
        MonkeyPatch::patchFunction('rand', 10000, 'User_backend');
        */
        MonkeyPatch::patchMethod(
            'Backend_user',
            ['create' => false]
        );

        $expected = false;
        $this->assertSame($expected, $this->CI->User_backend->sign_up($user_data));
    }

    public function test_sign_in_true()
	{
        $user_data = [];
        $user_data['group_level_1'] = 1;
        $user_data['group_level_2'] = 2;
        $user_data['group_level_3'] = 3;
        $user_data['name'] = 'name';
        $user_data['password'] = 'password';
        $user_data['mailadress'] = 'mailadress';
        $user_data['role'] = 2;

        $user_object = new stdClass();
        $user_object->password = '$2y$10$WQCCDH1mPBvMJaLxkPwFsOojbQzAoJDm0wK8KG21KoIv.x2Gira9G';

        MonkeyPatch::patchMethod(
            'Backend_user',
            ['get_by_name' => $user_object]
        );
        MonkeyPatch::verifyInvokedOnce(
            'Backend_user::get_by_name', [$user_data['name']]
        );

        $expected = true;
        $this->assertSame($expected, $this->CI->User_backend->sign_in($user_data));
    }

    public function test_sign_in_false_no_object()
	{
        $user_data = [];
        $user_data['group_level_1'] = 1;
        $user_data['group_level_2'] = 2;
        $user_data['group_level_3'] = 3;
        $user_data['name'] = 'name';
        $user_data['password'] = 'password';
        $user_data['mailadress'] = 'mailadress';
        $user_data['role'] = 2;

        MonkeyPatch::patchMethod(
            'Backend_user',
            ['get_by_name' => null]
        );
        MonkeyPatch::verifyInvokedOnce(
            'Backend_user::get_by_name', [$user_data['name']]
        );

        $expected = false;
        $this->assertSame($expected, $this->CI->User_backend->sign_in($user_data));
    }

    public function test_sign_in_false_password()
	{
        $user_data = [];
        $user_data['group_level_1'] = 1;
        $user_data['group_level_2'] = 2;
        $user_data['group_level_3'] = 3;
        $user_data['name'] = 'name';
        $user_data['password'] = 'password';
        $user_data['mailadress'] = 'mailadress';
        $user_data['role'] = 2;

        $user_object = new stdClass();
        $user_object->password = '';

        MonkeyPatch::patchMethod(
            'Backend_user',
            ['get_by_name' => $user_object]
        );
        MonkeyPatch::verifyInvokedOnce(
            'Backend_user::get_by_name', [$user_data['name']]
        );

        $expected = false;
        $this->assertSame($expected, $this->CI->User_backend->sign_in($user_data));
    }

    public function test_get_user_object()
	{
        $user_id = 1;

        $object = new stdClass();
        $object->id = '1';

        MonkeyPatch::patchMethod(
            'Backend_user',
            ['get_by_id' => $object]
        );
        MonkeyPatch::verifyInvokedOnce(
            'Backend_user::get_by_id', [$user_id]
        );

        $expected = $object;
        $this->assertSame($expected, $this->CI->User_backend->get_user($user_id));
    }

    public function test_get_user_null()
	{
        $user_id = 1;

        MonkeyPatch::patchMethod(
            'Backend_user',
            ['get_by_id' => null]
        );
        MonkeyPatch::verifyInvokedOnce(
            'Backend_user::get_by_id', [$user_id]
        );

        $this->assertNull($this->CI->User_backend->get_user($user_id));
    }

    public function test_get_user_list_without_admin_select()
	{
        $this->CI->load->model('service/Role_backend');
        $target_role = $this->CI->Role_backend::ADMIN;

        $object1 = new stdClass();
        $object1->id = '1';

        $object2 = new stdClass();
        $object2->id = '2';

        MonkeyPatch::patchMethod(
            'Backend_user',
            ['get_list_by_ignore_role' => [$object1, $object2]]
        );
        MonkeyPatch::verifyInvokedOnce(
            'Backend_user::get_list_by_ignore_role', [[$target_role]]
        );

        $expected = [$object1, $object2];
        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin());
    }

    public function test_get_user_list_without_admin_no_select()
	{
        $this->CI->load->model('service/Role_backend');
        $target_role = $this->CI->Role_backend::ADMIN;

        MonkeyPatch::patchMethod(
            'Backend_user',
            ['get_list_by_ignore_role' => []]
        );
        MonkeyPatch::verifyInvokedOnce(
            'Backend_user::get_list_by_ignore_role', [[$target_role]]
        );

        $expected = [];
        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin());
    }

    private function get_user_list_for_result()
    {
        $object1 = new stdClass();
        $object1->id = '1';
        $object2 = new stdClass();
        $object2->id = '2';
        $object3 = new stdClass();
        $object3->id = '3';
        $object4 = new stdClass();
        $object4->id = '4';
        $object5 = new stdClass();
        $object5->id = '5';
        $object6 = new stdClass();
        $object6->id = '6';
        $object7 = new stdClass();
        $object7->id = '7';
        $object8 = new stdClass();
        $object8->id = '8';
        $object9 = new stdClass();
        $object9->id = '9';
        $object10 = new stdClass();
        $object10->id = '10';
        $object11 = new stdClass();
        $object11->id = '11';

        $result = [
            $object1,
            $object2,
            $object3,
            $object4,
            $object5,
            $object6,
            $object7,
            $object8,
            $object9,
            $object10,
            $object11,
        ];

        return $result;
    }

    public function test_get_user_list_without_admin_by_offset_1_3()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[0],
            $result[1],
            $result[2],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('1', '3'));
    }

    public function test_get_user_list_without_admin_by_offset_2_3()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[3],
            $result[4],
            $result[5],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('2', '3'));
    }

    public function test_get_user_list_without_admin_by_offset_4_3()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[9],
            $result[10],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('4', '3'));
    }

    public function test_get_user_list_without_admin_by_offset_5_3()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[9],
            $result[10],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('5', '3'));
    }

    public function test_get_user_list_without_admin_by_offset_5_0()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[0],
            $result[1],
            $result[2],
            $result[3],
            $result[4],
            $result[5],
            $result[6],
            $result[7],
            $result[8],
            $result[9],
            $result[10],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('5', '0'));
    }

    public function test_get_user_list_without_admin_by_offset_0_3()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[0],
            $result[1],
            $result[2],
            $result[3],
            $result[4],
            $result[5],
            $result[6],
            $result[7],
            $result[8],
            $result[9],
            $result[10],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('0', '3'));
    }

    public function test_get_user_list_without_admin_by_offset_a_3()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[0],
            $result[1],
            $result[2],
            $result[3],
            $result[4],
            $result[5],
            $result[6],
            $result[7],
            $result[8],
            $result[9],
            $result[10],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('a', '3'));
    }

    public function test_get_user_list_without_admin_by_offset_1_b()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[0],
            $result[1],
            $result[2],
            $result[3],
            $result[4],
            $result[5],
            $result[6],
            $result[7],
            $result[8],
            $result[9],
            $result[10],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('1', 'b'));
    }

    public function test_get_user_list_without_admin_by_offset_null_null()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[0],
            $result[1],
            $result[2],
            $result[3],
            $result[4],
            $result[5],
            $result[6],
            $result[7],
            $result[8],
            $result[9],
            $result[10],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset());
    }

    public function test_get_user_list_without_admin_by_offset_2_4()
    {
        $result = $this->get_user_list_for_result();

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [
            $result[4],
            $result[5],
            $result[6],
            $result[7],
        ];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('2', '4'));
    }

    public function test_get_user_list_without_admin_by_offset_2_4_empty()
    {
        $result = [];

        MonkeyPatch::patchMethod(
            'User_backend',
            ['get_user_list_without_admin' => $result]
        );
        MonkeyPatch::verifyInvokedOnce(
            'User_backend::get_user_list_without_admin', []
        );

        $expected = [];

        $this->assertSame($expected, $this->CI->User_backend->get_user_list_without_admin_by_offset('2', '4'));
    }

}
