<?php

namespace Volga\Userman;

use Tests\Unit\BaseTest;
use Volga\Userman\Exceptions\CredentialsException;
use Volga\Userman\Exceptions\EmailIsNotValidException;
use Volga\Userman\Exceptions\UserNotFoundException;
use Volga\Userman\Repositories\IlluminateUserRepository;
use Volga\Userman\Services\PasswordHash;

/**
 * @property UserManager $userman
 */
class UserManagerTest extends BaseTest
{
    private UserManager $userman;
    private PasswordHash $hasher;

    public function setUp(): void
    {
        parent::setUp();

        $db = $this->capsule->getConnection();
        $this->hasher = new PasswordHash(1, []);
        $repository = new IlluminateUserRepository($db, $this->hasher);
        $this->userman = new UserManager($repository);
        $db->table('users')->truncate();
    }

    public function testCanBeInitiated()
    {
        $this->assertInstanceOf('Volga\Userman\UserManager', $this->userman);
    }

    public function testCreateUser()
    {
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissa@test.com', 'password' => 'password'];
        $user = $this->userman->createUser($input);
        $checked = $this->hasher->check($input['password'], $user->getPassword());
        $this->assertEquals($input['name'], $user->getName());
        $this->assertEquals($input['surname'], $user->getSurname());
        $this->assertEquals($input['email'], $user->getEmail());
        $this->assertTrue($checked);
    }

    public function testUpdatePassword()
    {
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissa2@test.com', 'password' => 'password'];
        $oldUser = $this->userman->createUser($input);

        $inputForUpdate = ['password' => 'password2', 'email' => 'larissa2@test.com',];
        $user = $this->userman->updateUser($inputForUpdate);
        $this->assertTrue($this->hasher->check($inputForUpdate['password'], $user->getPassword()));
        $this->assertFalse($this->hasher->check($inputForUpdate['password'], $oldUser->getPassword()));
        $this->assertEquals($input['email'], $user->getEmail());
        $this->assertEquals($input['surname'], $user->getSurname());
        $this->assertEquals($input['name'], $user->getName());
    }

    public function testListUsers()
    {
        $users = $this->userman->listUsers();
        $this->assertEquals(0, count($users));

        for ($i = 0; $i < 10; $i++) {
            $input = [
                'name' => 'Larissa'.$i, 'surname' => 'Smith'.$i, 'email' => 'larissa'.$i.'@test.com',
                'password' => 'password'.$i,
            ];

            $this->userman->createUser($input);
        }

        $users = $this->userman->listUsers();
        $this->assertEquals(10,  count($users));

        $this->assertEquals('Larissa0', $users[0]->getName());
        $this->assertEquals('Smith1', $users[1]->getSurname());
    }

    public function testFailsValidationIfEmptyEmail()
    {
        $this->expectException(\InvalidArgumentException::class);
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'password' => 'password'];
        $this->userman->createUser($input);
    }

    public function testFailsValidationIfEmptyPassword()
    {
        $this->expectException(\InvalidArgumentException::class);
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissa@test.com'];
        $this->userman->createUser($input);
    }

    public function testUpdateFails()
    {
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissa2@test.com', 'password' => 'password'];
         $this->userman->createUser($input);

        $inputForUpdate = ['password' => 'password2', 'email' => 'test@test.com',];
        $this->expectException(UserNotFoundException::class);
        $this->userman->updateUser($inputForUpdate);
    }

    public function testUpdateFailsEmptyPassword()
    {
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissa2@test.com', 'password' => 'password'];
        $this->userman->createUser($input);

        $inputForUpdate = ['email' => 'larissa2@test.com',];
        $this->expectException(\InvalidArgumentException::class);
        $this->userman->updateUser($inputForUpdate);
    }

    public function testUpdateFailsEmptyInput()
    {
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissa2@test.com', 'password' => 'password'];
        $this->userman->createUser($input);

        $inputForUpdate = ['password' => 'password2', 'email' => '',];
        $this->expectException(\InvalidArgumentException::class);
        $this->userman->updateUser($inputForUpdate);
    }

    public function testUpdateFailsUserDoesntExists()
    {
        $inputForUpdate = ['password' => 'password2', 'email' => 'larissa2@test.com',];
        $this->expectException(UserNotFoundException::class);
        $this->userman->updateUser($inputForUpdate);
    }

    public function testCanGetUserByCredentials()
    {
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissa2@test.com', 'password' => 'password'];
        $this->userman->createUser($input);
        $user = $this->userman->getUser(['email' => 'larissa2@test.com', 'password' => 'password']);
        $this->assertEquals( 'larissa2@test.com', $user->getEmail());
    }

    public function testCanNotGetUserWithWongPassword()
    {
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissa2@test.com', 'password' => 'password'];
        $this->userman->createUser($input);
        $this->expectException(CredentialsException::class);
        $this->userman->getUser(['email' => 'larissa2@test.com', 'password' => 'wrong']);
    }

    public function testEmailIsValidated()
    {
        $input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissatest.com', 'password' => 'password'];
        $this->expectException(EmailIsNotValidException::class);
        $this->userman->createUser($input);
    }
}
