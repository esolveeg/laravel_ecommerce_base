<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\WithFaker;
class AuthTest extends TestCase
{
    use WithFaker;
    public function testLogin()
    {
        $email = 'test@test.com';
        $wrongEmail = 'wrong@wrong.com';
        $password = '123456';
        $wrongPassowrd = '1234567';
        //success
        $response = $this->postJson('/api/login' , [
            "email" => $email,
	        "password" => $password
        ]);
        $response->assertStatus(200);
        

        // email not found
        $response = $this->postJson('/api/login' , [
            "email" => $wrongEmail,
	        "password" => $password
        ]);
        $response->assertStatus(400)->assertExactJson(['email_not_found']);


        //wrong passowrd
        $response = $this->postJson('/api/login' , [
            "email" =>$email,
	        "password" => $wrongPassowrd
        ]);
        $response->assertStatus(400)->assertExactJson(['password_not_match']);

        //wrong email and password
        $response = $this->postJson('/api/login' , [
            "email" =>$wrongEmail,
	        "password" => $wrongPassowrd
        ]);
        $response->assertStatus(400)->assertExactJson(['email_not_found']);

        //no email
        $response = $this->postJson('/api/login' , [
	        "password" => $password
        ]);
        $response->assertStatus(400)->assertExactJson(["email" => ["email_required"]]);
        //no password
        $response = $this->postJson('/api/login' , [
	        "email" => $email
        ]);
        $response->assertStatus(400)->assertExactJson(["password" => ["password_required"]]);
    }

    public function testRegister()
    {
        parent::setUp();
        $faker =$this->faker;
        $successEmail = $faker->unique()->email;
        $successPhone = $faker->unique()->phoneNumber;
        //success
        $response = $this->postJson('/api/register' , [
            "email" => $successEmail,
            "name" => $faker->name,
            "phone" => $successPhone,
	        "password" => $faker->password
        ]);
        $response->assertStatus(200)->assertJsonPath('email', $successEmail);
        //duplicate email 
        $response = $this->postJson('/api/register' , [
            "email" => $successEmail,
            "name" => $faker->name,
            "phone" => $faker->unique()->phoneNumber,
	        "password" => $faker->password
        ]);
        
        $response->assertStatus(400)->assertExactJson(["email_already_exists"]);
        //duplicate phone
        $response = $this->postJson('/api/register' , [
            "email" => $faker->unique()->email,
            "name" => $faker->name,
            "phone" => $successPhone,
	        "password" => $faker->password
        ]);
        $response->assertStatus(400)->assertExactJson(["phone" =>  ["phone_already_exists"]]);


        //short password
        $response = $this->postJson('/api/register' , [
            "email" => $faker->unique()->email,
            "name" => $faker->name,
            "phone" => $faker->unique()->phoneNumber,
	        "password" => 12345
        ]);
        $response->assertStatus(400)->assertExactJson(["password" =>  ["password_min_6"]]);

        //no name
        $response = $this->postJson('/api/register' , [
            "email" => $faker->unique()->email,
            "phone" => $faker->unique()->phoneNumber,
	        "password" => $faker->password
        ]);
        $response->assertStatus(400)->assertExactJson(["name" => ["name_required"]]);


         //no email
         $response = $this->postJson('/api/register' , [
            "name" => $faker->name,
            "phone" => $faker->unique()->phoneNumber,
	        "password" => $faker->password
        ]);

        //no phone
        $response = $this->postJson('/api/register' , [
            "name" => $faker->name,
            "email" => $faker->unique()->email,
	        "password" => $faker->password
        ]);
        $response->assertStatus(400)->assertExactJson(["phone" => ["phone_required"]]);

        //no password
        $response = $this->postJson('/api/register' , [
            "name" => $faker->name,
            "email" => $faker->unique()->email,
	        "phone" => $faker->unique()->phone
        ]);
        $response->assertStatus(400)->assertExactJson(["password" => ["password_required"]]);

        //
    }
}
