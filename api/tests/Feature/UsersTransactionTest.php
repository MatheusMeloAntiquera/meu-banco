<?php

namespace Tests\Feature;

use Mockery;
use App\Models\User;
use Mockery\MockInterface;
use Tests\BaseFeatureTest;
use Tests\Traits\UserTestTrait;
use App\Jobs\ProcessNotifyRecipient;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Services\User\FindUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\AuthorizationServiceRepository;
use App\Services\User\CheckSufficientBalanceService;

class UsersTransactionTest extends BaseFeatureTest
{
    use RefreshDatabase;
    use UserTestTrait;

    private User $userOne, $userTwo;
    private UserRepository $userRepository;
    private string $storeRoute;
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

        $this->storeRoute = route("transactions.store");

        //Cria uma fila "fake", ou seja, não grava os jobs de verdade no banco
        Queue::fake();
    }

    /**
     * Um usuário deverá conseguir pagar outro usuário
     * @group Transaction
     * @test
     * @return void
     */
    public function aUserMustTransferMoneyToAnotherUser()
    {
        Http::fake([
            'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6' => Http::response([
                "message" => "Autorizado"
            ], 200)
        ]);

        $response = $this->postJson(
            $this->storeRoute,
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

        // Verifica se foi criado o job para a fila notificação do usuário
        Queue::assertPushedOn('notify', ProcessNotifyRecipient::class);
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
            $this->storeRoute,
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
            $this->storeRoute,
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
            $this->storeRoute,
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

    /**
     * Não deverá ser possível transferir porque o serviço externo de autorização não está autorizando.
     * @group Transaction
     * @test
     * @return void
     */
    public function shouldNotPossibleToTransferBecauseExternalServiceWasNotAuthorized()
    {
        Http::fake([
            'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6' => Http::response([
                "message" => "Não autorizado"
            ], 403)
        ]);

        $response = $this->postJson(
            $this->storeRoute,
            [
                "sender_id" => $this->userOne->id,
                "recipient_id" => $this->userTwo->id,
                "value" => 100.00
            ]
        );

        $response->assertStatus(403);
        $this->assertEquals(false, $response["success"]);
        $this->assertEquals(
            "It was not possible to complete the transaction. Try again later",
            $response["message"]
        );
    }
}
