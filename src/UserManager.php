<?php declare(strict_types = 1);

namespace Volga\Userman;

use InvalidArgumentException;
use Volga\Userman\Contracts\UserInterface;
use Volga\Userman\Contracts\UserRepositoryInterface;
use Volga\Userman\Exceptions\CredentialsException;
use Volga\Userman\Exceptions\UserUpdateFailException;

class UserManager
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $credentials
     * @return UserInterface
     * @throws CredentialsException
     */
    public function getUser(array $credentials): UserInterface
    {
        $user = $this->repository->getByIdentifier($credentials['email']);

        if (!$this->repository->validateCredentials($user, $credentials)) {
            throw new CredentialsException('Password is incorrect');
        }

        return $user;
    }

    /**
     * @param array $input
     * @return UserInterface
     */
    public function createUser(array $input): UserInterface
    {
        $this->repository->validate($input);
        $userId = $this->repository->insertReturnId($input);

        return $this->repository->getById($userId);
    }

    /**
     * @param array $input
     * @return UserInterface
     * @throws UserUpdateFailException
     */
    public function updateUser(array $input)
    {
        if (empty($input['email'])) {
            throw new InvalidArgumentException('No email was passed.');
        }

        $user = $this->repository->getByIdentifier($input['email']);
        $this->repository->validate($input, $user);

        try {
            $this->repository->update($user, $input);
        } catch (\Throwable $e) {
            throw new UserUpdateFailException('Application error.'.$e->getMessage());
        }

        return $this->repository->getById($user->getId());
    }

    /**
     * @return array
     */
    public function listUsers(): array
    {
        return $this->repository->getList()->toArray();
    }
}
