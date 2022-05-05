<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use App\Models\User;
use Faker\Generator;
use Faker\Factory as Faker;

use App\Dtos\User\UserCreateDto;
use App\Services\User\CreateUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUserServiceTest extends TestCase
{

    use RefreshDatabase;

    private CreateUserService $createUserService;
    private Generator $fakerBr;

    protected function setUp(): void
    {
        $this->fakerBr = Faker::create('pt_BR');
        $this->createUserService = new CreateUserService();
        parent::setUp();
    }
    /**
     * Deverá criar um usuário com sucesso
     * @test
     * @return void
     */
    public function shouldCreateAUserSucessfully()
    {
        $userData = $this->returnUserInsertable();

        $userCreated = $this->createUserService->execute($userData);

        $this->assertInstanceOf(User::class, $userCreated);
        //Campos preenchidos
        $this->assertEquals($userData->firstName, $userCreated->first_name);
        $this->assertEquals($userData->lastName, $userCreated->last_name);
        $this->assertEquals($userData->email, $userCreated->email);
        $this->assertEquals($userData->password, $userCreated->password);
        $this->assertEquals($userData->cpf, $userCreated->cpf);
        //Campos setados automaticamente
        $this->assertEquals($userCreated->active, true);
        $this->assertEquals($userCreated->balance, 0.00);
    }

    /**
     * Deverá criar um usuário com sucesso
     * @test
     * @return void
     */
    public function shouldNotPossibleCreateAUserBecauseDataIsIncorret()
    {
        $userData = new UserCreateDto(
            firstName: '',
            lastName: '',
            email: 'teste',
            password: '123',
            cpf: '',
        );
        $this->createUserService->execute($userData);
        $this->expectException(InvalidArgumentException::class);

    }

    private function returnUserInsertable()
    {
        return new UserCreateDto(
            firstName: $this->fakerBr->firstName(),
            lastName: $this->fakerBr->lastName(),
            email: $this->fakerBr->email(),
            password: $this->fakerBr->word(),
            cpf: $this->fakerBr->cpf(),
        );
    }
}
