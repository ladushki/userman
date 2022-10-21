<?php

namespace Tests\Unit\Services;

use Tests\Unit\BaseTest;
use Volga\Userman\Services\PasswordHash;

class PasswordHashTest extends BaseTest
{

    public function testHash()
    {
        $hasher = new PasswordHash(1);
        $actual = $hasher->hash('test');
        $this->assertNotFalse($actual, 'Can not make a hash');
        $this->assertIsString($actual, 'Is not a string');
    }

    public function testCheck()
    {
        $hasher = new PasswordHash(1);
        $password = 'test';
        $this->assertTrue($hasher->check($password, $hasher->hash($password)), 'Is not equal');
        $this->assertFalse($hasher->check($password.$password, $hasher->hash($password)), 'Is not equal');
        $this->assertFalse($hasher->check('$password', ''), 'Is not equal');
    }
}
