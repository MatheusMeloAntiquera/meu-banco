<?php

namespace Tests\Unit\User;

use Exception;
use Tests\TestCase;
use App\Models\User;
use Faker\Generator;

use Faker\Factory as Faker;
use App\Dtos\User\UserCreateDto;
use App\Exceptions\InvalidDataException;
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
     * Não deverá ser possivel criar um usuário, porque os dados passados para o serviço estão incorretos
     * @test
     * @return void
     */
    public function shouldNotBePossibleCreateAUserBecauseDataIsIncorret()
    {
        $this->expectException(InvalidDataException::class);
        $userData = new UserCreateDto(
            firstName: '',
            lastName: '',
            email: 'teste',
            password: '123',
            cpf: '',
        );
        $this->createUserService->execute($userData);
    }

    /**
     * Não deverá ser possivel criar um usuário, porque o e-mail informado já está sendo usado por outro usuário
     * @test
     * @return void
     */
    public function shouldNotBePossibleCreateAUserBecauseEmailIsAlreadyUsed()
    {
        try {
            $userCreated = $this->createUserService->execute($this->returnUserInsertable());
            $userData = new UserCreateDto(
                firstName: $this->fakerBr->firstName(),
                lastName: $this->fakerBr->lastName(),
                email: $userCreated->email,
                password: $this->fakerBr->password(),
                cpf: $this->fakerBr->cpf(false),
            );
            $this->createUserService->execute($userData);
            $this->fail("Failed because it did not throw an exception");
        } catch (InvalidDataException $error) {
            $this->assertIsArray($error->getMessages());
            $this->assertCount(1, $error->getMessages()["email"]);
            $this->assertSame("The email has already been taken.", $error->getMessages()["email"][0]);
        }
    }

    /**
     * Não deverá ser possivel criar um usuário, porque o cpf informado já está sendo usado por outro usuário
     * @test
     * @return void
     */
    public function shouldNotBePossibleCreateAUserBecauseCpfIsAlreadyUsed()
    {
        try {
            $userCreated = $this->createUserService->execute($this->returnUserInsertable());
            $userData = new UserCreateDto(
                firstName: $this->fakerBr->firstName(),
                lastName: $this->fakerBr->lastName(),
                email: $this->fakerBr->email(),
                password: $this->fakerBr->password(),
                cpf: $userCreated->cpf,
            );
            $this->createUserService->execute($userData);
            $this->fail("Failed because it did not throw an exception");
        } catch (InvalidDataException $error) {
            $this->assertIsArray($error->getMessages());
            $this->assertCount(1, $error->getMessages()["cpf"]);
            $this->assertSame("The cpf has already been taken.", $error->getMessages()["cpf"][0]);
        }
    }

    private function returnUserInsertable()
    {
        return new UserCreateDto(
            firstName: $this->fakerBr->firstName(),
            lastName: $this->fakerBr->lastName(),
            email: $this->fakerBr->email(),
            password: $this->fakerBr->password(),
            cpf: $this->fakerBr->cpf(false),
        );
    }
}
