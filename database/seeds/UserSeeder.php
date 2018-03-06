<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert(array(
        	['name' => 'Admin', 'email' => 'admin@test_mc.com', 'password' => sha1(md5('admin@test_mc.com')), 'created_at' => time(), 'updated_at' => time()],
        	['name' => 'User 2', 'email' => 'user2@test_mc.com', 'password' => sha1(md5('user2@test_mc.com')), 'created_at' => time(), 'updated_at' => time()],
        	['name' => 'User 3', 'email' => 'user3@test_mc.com', 'password' => sha1(md5('user3@test_mc.com')), 'created_at' => time(), 'updated_at' => time()]
        ));
    }
}
