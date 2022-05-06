<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\BaseFeatureTest;
use Tests\Traits\UserTestTrait;
use App\Repositories\UserRepository;
use App\Services\User\FindUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\User\CheckSufficientBalanceService;

class UsersTransactionTest extends BaseFeatureTest
{
    use RefreshDatabase;
    use UserTestTrait;

    private User $userOne, $userTwo;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userOne = $this->createAUserSuccessfully();
        $this->userTwo = $this->createAUserSuccessfully();
        $this->userRepository = new UserRepository();

        $this->userRepository->updateBalance(
            $this->userOne->id,
            100.00
        );
    }

    /**
     * Um usuário deverá conseguir pagar outro usuário
     * @group Transaction
     * @test
     * @return void
     */
    public function aUserMustTransferMoneyToAnotherUser()
    {
        $response = $this->postJson(
            "/api/transaction/",
            [
                "sender_id" => $this->userOne->id,
                "recipient_id" => $this->userTwo->id,
                "value" => 100.00
            ]
        );

        $response->assertStatus(201);
        $this->assertEquals(true, $response["success"]);
        $this->assertArrayHasKey("transaction_id", $response);
        $this->assertIsInt($response["transaction_id"]);
        $userOneUpdated = $this->userRepository->findById($this->userOne->id);
        $userTwoUpdated = $this->userRepository->findById($this->userTwo->id);

        $this->assertEquals(0.0, $userOneUpdated->balance);
        $this->assertEquals(100.0, $userTwoUpdated->balance);
    }

    /**
     * Não deve ser possível transferir o dinheiro para outro usuário porque o remetente não tem saldo suficiente
     * @group Transaction
     * @test
     * @return void
     */
    public function shouldNotPossibleToTransferBecauseSenderHasNotSufficientBalance()
    {
        $response = $this->postJson(
            "/api/transaction/",
            [
                "sender_id" => $this->userOne->id,
                "recipient_id" => $this->userTwo->id,
                "value" => 150.00
            ]
        );

        $response->assertStatus(403);
        $this->assertEquals(false, $response["success"]);
        $this->assertEquals(CheckSufficientBalanceService::ERROR_MESSAGE, $response["message"]);
    }

    /**
     * Não deverá ser possível transferir porque o rementente não foi encontrado
     * @group Transaction
     * @test
     * @return void
     */
    public function shouldNotPossibleToTransferBecauseSenderNotFound()
    {
        $response = $this->postJson(
            "/api/transaction/",
            [
                "sender_id" => rand(500, 1000),
                "recipient_id" => $this->userTwo->id,
                "value" => 100.00
            ]
        );

        $response->assertStatus(404);
        $this->assertEquals(false, $response["success"]);
        $this->assertEquals(
            FindUserService::USER_NOT_FOUND_MESSAGE,
            $response["message"]
        );
    }

    /**
     * Não deverá ser possível transferir porque o recebedor não foi encontrado
     * @group Transaction
     * @test
     * @return void
     */
    public function shouldNotPossibleToTransferBecauseRecipientNotFound()
    {
        $response = $this->postJson(
            "/api/transaction/",
            [
                "sender_id" => $this->userOne->id,
                "recipient_id" => rand(500, 1000),
                "value" => 100.00
            ]
        );

        $response->assertStatus(404);
        $this->assertEquals(false, $response["success"]);
        $this->assertEquals(
            FindUserService::USER_NOT_FOUND_MESSAGE,
            $response["message"]
        );
    }
}
