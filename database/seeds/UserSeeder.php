<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $user = [
            'email' => 'test@test.com',
            'password' => bcrypt(123456),
            'name' => 'test account',
            'phone' => '01010101010',
       ];

       $client = [
        'name' => 'Laravel Password Grant Client',
        'secret' => 'g4gbQhrlY99Ynx9Fuh2XcxSq4bR2AaG56qLIfqAY',
        'provider' => 'users',
        'redirect' => env('APP_URL', 'http://localhost'),
        'revoked' =>0,
        'personal_access_client' => 0,
        'password_client' => 1,
        ];

        Client::create($client);

        $user = User::create($user);

    }
}
