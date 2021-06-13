<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthTest extends TestCase
{
    use WithFaker;
    public function testLogin()
    {
        // $wrongEmail = 'wrong@wrong.com';
        // $wrongPassowrd = '1234567';
        //success
        $response = $this->postJson('/api/login' ,$this->loginData());
        $response->assertStatus(200);
        

        // email not found
        // $response = $this->postJson('/api/login' , [
        //     "email" => $wrongEmail,
	    //     "password" => $password
        // ]);
        // $response->assertStatus(400)->assertExactJson(['email_not_found']);


        //wrong passowrd
        // $response = $this->postJson('/api/login' , [
        //     "email" =>$email,
	    //     "password" => $wrongPassowrd
        // ]);
        // $response->assertStatus(400)->assertExactJson(['password_not_match']);

        //wrong email and password
        // $response = $this->postJson('/api/login' , [
        //     "email" =>$wrongEmail,
	    //     "password" => $wrongPassowrd
        // ]);
        // $response->assertStatus(400)->assertExactJson(['email_not_found']);

        // //no email
        // $response = $this->postJson('/api/login' , [
	    //     "password" => $password
        // ]);
        // $response->assertStatus(400)->assertExactJson(["email" => ["email_required"]]);
        // //no password
        // $response = $this->postJson('/api/login' , [
	    //     "email" => $email
        // ]);
        // $response->assertStatus(400)->assertExactJson(["password" => ["password_required"]]);
    }

    public function testRegister()
    {
        parent::setUp();
        // $faker =$this->faker;
        // $successEmail = $faker->unique()->email;
        // $successPhone = $faker->unique()->phoneNumber;
        //success
        $data =  $this->registerData();
        $url = '/api/register';
        $response = $this->postJson('/api/register' ,$data);
        $response->assertStatus(200)->assertJsonPath('email',$data['email']);
        $rules = [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255',
            'name' => 'required|max:255',
            'phone' => 'required|max:255|unique:users'];
        $options = ['url' => $url , 'data' => $data , 'rules' => $rules];
        $this->testValidation($options);
        //duplicate email 
        // $response = $this->postJson('/api/register' , [
        //     "email" => $successEmail,
        //     "name" => $faker->name,
        //     "phone" => $faker->unique()->phoneNumber,
	    //     "password" => $faker->password
        // ]);
        
        // $response->assertStatus(400)->assertExactJson(["email_already_exists"]);
        // //duplicate phone
        // $response = $this->postJson('/api/register' , [
        //     "email" => $faker->unique()->email,
        //     "name" => $faker->name,
        //     "phone" => $successPhone,
	    //     "password" => $faker->password
        // ]);
        // $response->assertStatus(400)->assertExactJson(["phone" =>  ["phone_already_exists"]]);


        // //short password
        // $response = $this->postJson('/api/register' , [
        //     "email" => $faker->unique()->email,
        //     "name" => $faker->name,
        //     "phone" => $faker->unique()->phoneNumber,
	    //     "password" => 12345
        // ]);
        // $response->assertStatus(400)->assertExactJson(["password" =>  ["password_min_6"]]);

        // //no name
        // $response = $this->postJson('/api/register' , [
        //     "email" => $faker->unique()->email,
        //     "phone" => $faker->unique()->phoneNumber,
	    //     "password" => $faker->password
        // ]);
        // $response->assertStatus(400)->assertExactJson(["name" => ["name_required"]]);


        //  //no email
        //  $response = $this->postJson('/api/register' , [
        //     "name" => $faker->name,
        //     "phone" => $faker->unique()->phoneNumber,
	    //     "password" => $faker->password
        // ]);

        // //no phone
        // $response = $this->postJson('/api/register' , [
        //     "name" => $faker->name,
        //     "email" => $faker->unique()->email,
	    //     "password" => $faker->password
        // ]);
        // $response->assertStatus(400)->assertExactJson(["phone" => ["phone_required"]]);

        // //no password
        // $response = $this->postJson('/api/register' , [
        //     "name" => $faker->name,
        //     "email" => $faker->unique()->email,
	    //     "phone" => $faker->unique()->phone
        // ]);
        // $response->assertStatus(400)->assertExactJson(["password" => ["password_required"]]);

        //
    }

    private function loginData($overrides = [])
    {
        return array_merge([
            'email' => 'test@test.com',
            'password' => '123456',
        ], $overrides);
    }

    private function registerData($overrides = [])
    {
        $faker =$this->faker;
        $successEmail = $faker->unique()->email;
        $successPhone = $faker->unique()->phoneNumber;
        return array_merge([
            "email" => $successEmail,
            "name" => $faker->name,
            "phone" => $successPhone,
	        "password" => $faker->password
        ], $overrides);
    }

    private function testValidation($options){
        //declare variables
        $url = $options['url'];
        $rules = $options['rules'];
        //loop over rules
        foreach($rules as $index => $rule){
            //catch validations into separated array by exploding "|" character
            $validations = explode('|' ,$rule);
            // loop over validation rules
            foreach($validations as $validation){
                //check if the rule is required
                if($validation == 'required'){
                    // send the request with null value on the required index
                    $response = $this->postJson($url , $this->registerData([$index => '']));
                    //ecxpect the response
                    $expected = [$index => [$index."_required"]];
                    //assert that we have validation error and response is equal to expected response
                    $response->assertStatus(400)->assertExactJson($expected);
                }
                //check if rul is max
                if(str_contains($validation , 'max')){
                    //get the max character available
                    $chars = explode(":" , $validation)[1];
                    //clone a valid data
                    $data = $this->registerData();
                    //append string to the current field with lenght of maximum to make sure it returns error
                    $invalid = $data[$index] . Str::random($chars);
                    //send request with corrupted data
                    $response = $this->postJson($url , $this->registerData([$index => $invalid]));
                    //ecxpect the response
                    $expected = [$index => [$index."_max_".$chars]];
                    //assert that we have validation error and response is equal to expected response
                    $response->assertStatus(400)->assertExactJson($expected);
                }
                //check if rul is max
                if(str_contains($validation , 'min')){
                    //get the min character available
                    $chars = explode(":" , $validation)[1];
                    // create invalid index
                    $invalid = Str::random($chars - 1);
                    //send request with invalid data
                    $response = $this->postJson($url , $this->registerData([$index => $invalid])); 
                    //ecxpect the response
                    $expected = [$index => [$index."_min_".$chars]];
                    //assert that we have validation error and response is equal to expected response
                    $response->assertStatus(400)->assertExactJson($expected);
                }
                //check if rule is unique
                if(str_contains($validation , 'unique')){
                    //get the table
                    $table = explode(":" , $validation)[1];
                    // create invalid index
                    $invalid =(array) DB::table($table)->first();
                    //send request with invalid data
                    $response = $this->postJson($url , $this->registerData([$index => $invalid[$index]])); 
                    //ecxpect the response
                    $expected = [$index => [$index."_already_exists"]];
                    //assert that we have validation error and response is equal to expected response
                    $response->assertStatus(400)->assertExactJson($expected);
                }
            }
        }
    }
    
}
