<?php declare(strict_types = 1);

namespace Volga\Userman\Contracts;

use Volga\Userman\Models\UsersList;

interface UserRepositoryInterface
{
    /**
     * @param array $input
     * @return int|null
     */
    public function insertReturnId(array $input): ?int;

    /**
     * @param int $id
     * @return UserInterface
     */
    public function getById(int $id): UserInterface;

    /**
     * @param string $identifier
     * @return UserInterface
     */
    public function getByIdentifier(string $identifier): UserInterface;

    /**
     * @param UserInterface $user
     * @param array         $attributes
     * @return UserInterface
     */
    public function update(UserInterface $user, array $attributes): UserInterface;

    /**
     * @return UsersList
     */
    public function getList(): UsersList;

    /**
     * @param array              $attributes
     * @param UserInterface|null $user
     * @return bool
     */
    public function validate(array $attributes, UserInterface $user = null): bool;

    /**
     * @param UserInterface $user
     * @param array         $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials): bool;

    public function emailIsUnique(string $identifier, ?int $id = null): bool;

    public function validateEmail(string $email): bool;
}
