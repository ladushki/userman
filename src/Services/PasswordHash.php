<?php declare(strict_types = 1);

namespace Volga\Userman\Services;

use Volga\Userman\Contracts\PasswordHashingInterface;
use Volga\Userman\Exceptions\PasswordHashException;

class PasswordHash implements PasswordHashingInterface
{
    /**
     * Algorithm to use for passwords
     *
     * @var int
     */
    private int $algorithm;

    /**
     * Options for the current algorithm
     *
     * @var array
     */
    private array $options;

    public function __construct(int $algorithm = PASSWORD_BCRYPT, array $options = [])
    {
        $this->algorithm = $algorithm;
        $this->options = $options;
    }

    /**
     * @param string $value
     * @return bool|string
     * @throws PasswordHashException If the hash fails.
     */
    final public function hash(string $value): bool|string
    {
        $hash = password_hash($value, $this->algorithm, $this->options);

        return $hash === false ? throw new PasswordHashException('Bcrypt hashing not supported.') : $hash;
    }

    /**
     * @param string $value
     * @param string $hashedValue
     * @return bool
     */
    final public function check(string $value, string $hashedValue): bool
    {
        if ($hashedValue === '') {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

}
