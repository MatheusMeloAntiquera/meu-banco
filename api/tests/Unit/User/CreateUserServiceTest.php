<?php

namespace Tests\Unit\User;

use Mockery;
use Exception;
use Tests\TestCase;
use App\Models\User;

use Faker\Generator;
use Mockery\MockInterface;
use Faker\Factory as Faker;
use Tests\Traits\UserTestTrait;
use App\Dtos\User\UserCreateDto;
use Illuminate\Support\Facades\App;
use App\Repositories\UserRepository;
use App\Exceptions\InvalidDataException;
use App\Services\User\CreateUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUserServiceTest extends TestCase
{

    use RefreshDatabase;
    use UserTestTrait;

    private CreateUserService $createUserService;
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
    public function shouldCreateAUserSucessfully()
    {
        $userData = $this->returnUserInsertable();
        $mockUserRepository = $this->partialMock(
            UserRepository::class,
            function (MockInterface $mock) use ($userData) {
                $user = User::factory()->make([
                    'first_name' =>  $userData->firstName,
                    'last_name' =>  $userData->lastName,
                    'email' =>  $userData->email,
                    'password' =>  $userData->password,
                    'cpf' =>  $userData->cpf,
                    'active' => true,
                    'balance' => 0.00
                ]);
                $mock->shouldReceive('create')->once()->andReturn($user);
            }
        );

        /**
         * @var \Mockery\MockInterface $mockCreateUserService
         */
        $mockCreateUserService = Mockery::mock(CreateUserService::class, [$mockUserRepository]);
        $mockCreateUserService->shouldAllowMockingProtectedMethods()->shouldReceive('validateUserData')->once();
        $this->instance(
            CreateUserService::class,
            $mockCreateUserService->makePartial()
        );
        $this->createUserService = App::make(CreateUserService::class);

        $userCreated = $this->createUserService->execute($userData);
        dump($userCreated);
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
        /**
         * @var \Mockery\MockInterface $mockCreateUserService
         */
        $mockCreateUserService = Mockery::mock(CreateUserService::class);
        $mockCreateUserService->shouldAllowMockingProtectedMethods()
            ->shouldReceive('validateUserData')
            ->once()
            ->andThrow(new InvalidDataException(CreateUserService::DATA_USER_INVALID, []));

        $this->instance(
            CreateUserService::class,
            $mockCreateUserService->makePartial()
        );
        $this->createUserService = App::make(CreateUserService::class);
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

        /**
         * @var \Mockery\MockInterface $mockCreateUserService
         */
        $mockCreateUserService = Mockery::mock(CreateUserService::class);
        $mockCreateUserService->shouldAllowMockingProtectedMethods()
            ->shouldReceive('validateUserData')
            ->once()
            ->andThrow(new InvalidDataException(CreateUserService::DATA_USER_INVALID, ["email" => [
                "The email has already been taken."
            ]]));

        $this->instance(
            CreateUserService::class,
            $mockCreateUserService->makePartial()
        );
        $this->createUserService = App::make(CreateUserService::class);

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
        /**
         * @var \Mockery\MockInterface $mockCreateUserService
         */
        $mockCreateUserService = Mockery::mock(CreateUserService::class);
        $mockCreateUserService->shouldAllowMockingProtectedMethods()
            ->shouldReceive('validateUserData')
            ->once()
            ->andThrow(new InvalidDataException(CreateUserService::DATA_USER_INVALID, ["cpf" => [
                "The cpf has already been taken."
            ]]));

        $this->instance(
            CreateUserService::class,
            $mockCreateUserService->makePartial()
        );
        $this->createUserService = App::make(CreateUserService::class);

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
