<?php

namespace Tests\Feature;

use Tests\TestCase;
use Faker\Generator;
use Faker\Factory as Faker;
use Tests\Traits\UserTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreKeeperTest extends TestCase
{
    use UserTestTrait;
    use RefreshDatabase;

    const API_STOREKEEPER_ROUTE = '/api/storekeeper/';
    private Generator $fakerBr;

    protected function setUp(): void
    {
        $this->fakerBr = Faker::create('pt_BR');
        parent::setUp();
    }

    /**
     * Deverá criar um lojista com sucesso
     * @test
     * @return void
     */
    public function shouldCreateAStoreKeeperSucessfully()
    {
        $response = $this->postJson(self::API_STOREKEEPER_ROUTE, $this->returnAStoreKeeperInsertable($this->fakerBr)->toArray());

        $response->assertStatus(201);
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertIsArray($response['data']);
        $this->assertNotContains('balance', $response['data']);
    }

    /**
     * Deverá mostrar os dados um lojista com sucesso
     * @test
     * @return void
     */
    public function shouldShowAStoreKeeper()
    {
        $storeKeeperCreated = $this->createAStoreKeeperSuccessfully();
        $response = $this->getJson(self::API_STOREKEEPER_ROUTE . $storeKeeperCreated->id);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertIsArray($response['data']);
        $this->assertNotContains('balance', $response['data']);
    }

    /**
     * Deverá atualizar os dados um lojista com sucesso
     * @test
     * @return void
     */
    public function shouldUpdateAStoreKeeperSuccessfully()
    {
        $storeKeeperCreated = $this->createAStoreKeeperSuccessfully();
        $newLastName = $this->fakerBr->lastName();
        $newEmail = $this->fakerBr->email();
        $response = $this->putJson(self::API_STOREKEEPER_ROUTE . $storeKeeperCreated->id, [
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
     * Deverá deletar os dados um lojista com sucesso
     * @test
     * @return void
     */
    public function shouldDeleteAStoreKeeperSucessfully()
    {
        $storeKeeperCreated = $this->createAStoreKeeperSuccessfully();
        $response = $this->deleteJson(self::API_STOREKEEPER_ROUTE . $storeKeeperCreated->id);
        $response->assertStatus(200);
        $response->assertExactJson(['success' => true, "message" => "The storekeeper was deleted successfully"]);
    }
}
