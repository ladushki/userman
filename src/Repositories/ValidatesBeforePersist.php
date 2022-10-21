<?php declare(strict_types = 1);

namespace Volga\Userman\Repositories;

use InvalidArgumentException;
use Volga\Userman\Contracts\UserInterface;
use Volga\Userman\Exceptions\EmailIsNotValidException;

trait ValidatesBeforePersist
{

    /**
     * @param array              $attributes
     * @param UserInterface|null $user
     * @return bool
     * @throws EmailIsNotValidException
     */
    public function validate(array $attributes, UserInterface $user = null): bool
    {
        $id = $user?->getId();
        $this->validateRequired($attributes);
        $this->validateEmail($attributes['email']);
        $this->emailIsUnique($attributes['email'], $id);

        return true;
    }

    /**
     * @param string   $identifier Like email.
     * @param int|null $id
     * @return boolean
     */
    public function emailIsUnique(string $identifier, ?int $id = null): bool
    {
        $query = $this->connection->table($this->table);

        if ($id) {
            $query = $query->where('id', '!=', $id);
        }

        return $query->where('email', '=', $identifier)->count() === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function validateCredentials(UserInterface $user, array $credentials): bool
    {
        return $this->hasher->check($credentials['password'], $user->getPassword());
    }

    public function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new EmailIsNotValidException('You can not use this email address. '.$email);
        }

        return true;
    }

    /**
     * @param array    $input
     * @param int|null $id
     * @return bool
     */
    public function validateRequired(array $input): bool
    {
        if (empty($input['email'])) {
            throw new InvalidArgumentException('No email was passed.');
        }

        if (empty($input['password'])) {
            throw new InvalidArgumentException('You have not passed a [password].');
        }

        return true;
    }
}
