<?php declare(strict_types = 1);

namespace Volga\Userman\Contracts;

interface PasswordHashingInterface
{
    /**
     * @param string $value
     * @return bool|string
     */
    public function hash(string $value): bool|string;

    /**
     * @param string $value
     * @param string $hashedValue
     * @return bool|string
     */
    public function check(string $value, string $hashedValue): bool|string;
}
