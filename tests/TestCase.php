<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    protected function removeFieldFromTest($options)
    {
        //declare variables
        $fields = $options['fields'];
        $endpoint = $options['endpoint'];
        $mehthod = $options['method'];
        $req = $options['req'];
        //loop over fields
        foreach($fields as $field)
        {
            // set the current field to null
            $clone = $req ;
            $clone[$field['key']] = null;
           
            //consume endpoint
            $response = $this->postJson('/api/register' , $clone);
            if($field['required']){
                $response->assertStatus(400)->assertExactJson([$field['key'] => [$field['key']."_required"]]);
            } else {
                $response->assertStatus(200);
            }
            //check the error

        }
    }
}
