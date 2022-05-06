<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\BaseFeatureTest;
use Tests\Traits\UserTestTrait;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

        $this->userRepository->updateBalance(100.00);
    }

    /**
     * Um usuário deverá conseguir pagar outro usuário
     * @group Transaction
     * @test
     * @return void
     */
    public function aUserMustPayAnotherUser()
    {
        $response = $this->postJson(
            "/api/transaction/",
            [
                "sender_id" => $this->useOne->id,
                "recipient_id" => $this->useTwo->id,
                "value" => 100.00
            ]
        );

        $response->assertStatus(200);
        $this->assertEquals(["success" => true], $response);
        $userOneUpdated = $this->userRepository->findById($this->useOne->id);
        $userTwoUpdated = $this->userRepository->findById($this->useTwo->id);

        $this->assertEquals(0.0, $userOneUpdated->balance);
        $this->assertEquals(100.0, $userTwoUpdated->balance);
    }
}
