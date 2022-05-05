<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Faker\Generator;
use Faker\Factory as Faker;
use Tests\Traits\UserTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use UserTestTrait;
    use RefreshDatabase;

    const API_USER_ROUTE = '/api/users/';
    private Generator $fakerBr;

    protected function setUp(): void
    {
        $this->fakerBr = Faker::create('pt_BR');
        parent::setUp();
    }

    /**
     * Deverá criar um usuário com sucesso
     * @test
     * @return void
     */
    public function shouldCreateAnUserSucessfully()
    {
        $response = $this->postJson(self::API_USER_ROUTE, $this->returnUserInsertable($this->fakerBr)->toArray());

        $response->assertStatus(201);
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertIsArray($response['data']);
        $this->assertNotContains('balance', $response['data']);
    }

    /**
     * Deverá mostrar os dados um usuário com sucesso
     * @test
     * @return void
     */
    public function shouldShowAnUser()
    {
        $userCreated = $this->createUserSuccessfully();
        $response = $this->getJson(self::API_USER_ROUTE . $userCreated->id);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertIsArray($response['data']);
        $this->assertNotContains('balance', $response['data']);
    }

    /**
     * Deverá atualizar os dados um usuário com sucesso
     * @test
     * @return void
     */
    public function shouldUpdateAnUserSuccessfully()
    {
        $userCreated = $this->createUserSuccessfully();
        $newLastName = $this->fakerBr->lastName();
        $newEmail = $this->fakerBr->email();
        $response = $this->putJson(self::API_USER_ROUTE . $userCreated->id, [
            "last_name" => $newLastName,
            "email" => $newEmail
        ]);
        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertIsArray($response['data']);
        $this->assertNotContains('balance', $response['data']);
        $this->assertEquals($newLastName, $response['data']['last_name']);
        $this->assertEquals($newEmail, $response['data']['email']);
    }

    /**
     * Deverá deletar os dados um usuário com sucesso
     * @test
     * @return void
     */
    public function shouldDeleteAnUserSucessfully()
    {
        $userCreated = $this->createUserSuccessfully();
        $response = $this->deleteJson(self::API_USER_ROUTE . $userCreated->id);
        $response->assertStatus(200);
        $response->assertExactJson(['success' => true, "message" => "The user was deleted successfully"]);
    }
}
