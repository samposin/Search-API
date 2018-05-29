<?php

	use Carbon\Carbon;
	use Illuminate\Database\Seeder;

	class UsersTableSeeder extends Seeder {

		public function run()
	    {
	        DB::table('users')->delete();

	        $users = array(
	            array(
	                'id' => 1,
	                'name' => 'Admin',
	                'email'=>'admin@ileviathan.com',
	                'password'=> Hash::make( 'admin#123' ),
	                "created_at"=>Carbon::now(),
	                "updated_at"=>Carbon::now()
				)
	        );

	        DB::table('users')->insert($users);

	    }

	}