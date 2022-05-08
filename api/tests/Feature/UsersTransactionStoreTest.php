<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\BaseFeatureTest;
use App\Models\StoreKeeper;
use Tests\Traits\UserTestTrait;
use App\Jobs\ProcessNotifyRecipient;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Services\User\FindUserService;
use App\Repositories\StoreKeeperRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\StoreKeeper\FindStoreKeeperService;
use App\Services\User\CheckSufficientBalanceService;

class UsersTransactionStoreTest extends BaseFeatureTest
{
    use RefreshDatabase;
    use UserTestTrait;

    private User $user;
    private StoreKeeper $storekeeper;
    private UserRepository $userRepository;
    private StoreKeeperRepository $storekeeperRepository;
    private string $storeRoute;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createAUserSuccessfully();
        $this->storekeeper = $this->createAStoreKeeperSuccessfully();
        $this->userRepository = new UserRepository();
        $this->storekeeperRepository = new StoreKeeperRepository();

        $this->userRepository->updateBalance(
            $this->user->id,
            100.00
        );

        $this->storeRoute = route("transactions.toStore");

        //Cria uma fila "fake", ou seja, não grava os jobs de verdade no banco
        Queue::fake();
    }

    /**
     * Um usuário deverá conseguir transferir para um lojista
     * @group Transaction
     * @test
     * @return void
     */
    public function aUserMustTransferMoneyToAStorekeeper()
    {
        Http::fake([
            'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6' => Http::response([
                "message" => "Autorizado"
            ], 200)
        ]);

        $response = $this->postJson(
            $this->storeRoute,
            [
                "sender_id" => $this->user->id,
                "recipient_id" => $this->storekeeper->id,
                "value" => 100.00
            ]
        );

        $response->assertStatus(201);
        $this->assertArrayHasKey("transaction_id", $response);
        $this->assertIsInt($response["transaction_id"]);
        $userUpdated = $this->userRepository->findById($this->user->id);
        $storekeeperUpdated = $this->storekeeperRepository->findById($this->storekeeper->id);

        $this->assertEquals(0.0, $userUpdated->balance);
        $this->assertEquals(100.0, $storekeeperUpdated->balance);

        // Verifica se foi criado o job para a fila notificação do usuário
        Queue::assertPushedOn('notify', ProcessNotifyRecipient::class);
    }

    /**
     * Não deve ser possível transferir o dinheiro para um lojista porque o remetente não tem saldo suficiente
     * @group Transaction
     * @test
     * @return void
     */
    public function shouldNotPossibleToTransferBecauseSenderHasNotSufficientBalance()
    {
        $response = $this->postJson(
            $this->storeRoute,
            [
                "sender_id" => $this->user->id,
                "recipient_id" => $this->storekeeper->id,
                "value" => 150.00
            ]
        );

        $response->assertStatus(403);
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
                "recipient_id" => $this->storekeeper->id,
                "value" => 100.00
            ]
        );

        $response->assertStatus(404);
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
    public function shouldNotPossibleToTransferBecauseStorekeeperNotFound()
    {
        $response = $this->postJson(
            $this->storeRoute,
            [
                "sender_id" => $this->user->id,
                "recipient_id" => rand(500, 1000),
                "value" => 100.00
            ]
        );

        $response->assertStatus(404);
        $this->assertEquals(
            FindStoreKeeperService::STOREKEEPER_NOT_FOUND_MESSAGE,
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
                "sender_id" => $this->user->id,
                "recipient_id" => $this->storekeeper->id,
                "value" => 100.00
            ]
        );

        $response->assertStatus(403);
        $this->assertEquals(
            "It was not possible to complete the transaction. Try again later",
            $response["message"]
        );
    }
}
