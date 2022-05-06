<?php

namespace Tests\Feature;

use Faker\Generator;
use Tests\BaseFeatureTest;
use Faker\Factory as Faker;
use Tests\Traits\UserTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreKeeperTest extends BaseFeatureTest
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
     * Deverá criar um lojista com sucesso
     * @test
     * @return void
     */
    public function shouldCreateAStorekeeperSucessfully()
    {
        $response = $this->postJson(
            $this->getRouteStoreKeeperApiResource(
                self::STORE_ACTION
            ),
            $this->returnAStoreKeeperInsertable($this->fakerBr)->toArray()
        );

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
    public function shouldShowAStorekeeper()
    {
        $storeKeeperCreated = $this->createAStoreKeeperSuccessfully();
        $response = $this->getJson(
            $this->getRouteStoreKeeperApiResource(self::SHOW_ACTION, $storeKeeperCreated->id)
        );

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
    public function shouldUpdateAStorekeeperSuccessfully()
    {
        $storeKeeperCreated = $this->createAStoreKeeperSuccessfully();
        $newLastName = $this->fakerBr->lastName();
        $newEmail = $this->fakerBr->email();
        $response = $this->putJson(
            $this->getRouteStoreKeeperApiResource(self::UPDATE_ACTION, $storeKeeperCreated->id),
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
     * Deverá deletar os dados um lojista com sucesso
     * @test
     * @return void
     */
    public function shouldDeleteAStorekeeperSucessfully()
    {
        $storeKeeperCreated = $this->createAStoreKeeperSuccessfully();
        $response = $this->deleteJson(
            $this->getRouteStoreKeeperApiResource(self::DESTROY_ACTION, $storeKeeperCreated->id)
        );
        $response->assertStatus(200);
        $response->assertExactJson(['success' => true, "message" => "The storekeeper was deleted successfully"]);
    }
}
