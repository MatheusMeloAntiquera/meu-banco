<?php

namespace Tests\Feature;

use Faker\Generator;
use Tests\BaseFeatureTest;
use Faker\Factory as Faker;
use Tests\Traits\UserTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends BaseFeatureTest
{
    use UserTestTrait;
    use RefreshDatabase;

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
        $response = $this->postJson(
            $this->getRouteApiResource(self::STORE_ACTION),
            $this->returnAnUserInsertable($this->fakerBr)->toArray()
        );

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
        $userCreated = $this->createAnUserSuccessfully();
        $response = $this->getJson(
            $this->getRouteApiResource(self::SHOW_ACTION, $userCreated->id)
        );

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
        $userCreated = $this->createAnUserSuccessfully();
        $newLastName = $this->fakerBr->lastName();
        $newEmail = $this->fakerBr->email();
        $response = $this->putJson(
            $this->getRouteApiResource(self::UPDATE_ACTION, $userCreated->id),
            [
                "last_name" => $newLastName,
                "email" => $newEmail
            ]
        );
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
        $userCreated = $this->createAnUserSuccessfully();
        $response = $this->deleteJson(
            $this->getRouteApiResource(self::DESTROY_ACTION, $userCreated->id)
        );
        $response->assertStatus(200);
        $response->assertExactJson(['success' => true, "message" => "The user was deleted successfully"]);
    }
}
