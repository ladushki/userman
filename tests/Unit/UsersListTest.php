<?php

namespace Volga\Userman\Models;

use Tests\Unit\BaseTest;

class UsersListTest extends BaseTest
{

    public function testToArray()
    {
        $user = new User(['email' => 'larissa2@test.com','name' => 'Test', 'password'=>'test']);
        $list = new UsersList([$user]);
        $this->assertIsArray($list->toArray());
        $this->assertEquals(1, $list->count());
    }

    public function testOffsetSet()
    {
        $user = new User(['email' => 'larissa2@test.com','name' => 'Test', 'password'=>'test']);
        $list = new UsersList;
        $this->assertEquals(0, $list->count());
        $list->offsetSet(null, $user);
        $this->assertEquals(1, $list->count());
    }

    public function testPush()
    {
        $users = [];
        $users[] = new User(['email' => 'larissa2@test.com','name' => 'Test', 'password'=>'test']);
        $users[] = new User(['email' => 'larissa3@test.com','name' => 'Test3', 'password'=>'test']);
        $list = new UsersList;
        $list->push(...$users);
        $this->assertEquals(2, $list->count());
    }

    public function testOffsetGet()
    {
        $users[] = new User(['email' => 'larissa2@test.com','name' => 'Test', 'password'=>'test']);
        $list = new UsersList;
        $list->push(...$users);
        $this->assertEquals('Test', $list->offsetGet(0)->getName());
    }

    public function testOffsetExists()
    {
        $users[] = new User(['email' => 'larissa2@test.com','name' => 'Test', 'password'=>'test']);
        $list = new UsersList;
        $list->push(...$users);
        $this->assertFalse( $list->offsetExists(1));
        $this->assertTrue( $list->offsetExists(0));
    }

    public function testOffsetUnset()
    {
        $users = [];
        $users[] = new User(['email' => 'larissa2@test.com','name' => 'Test1', 'password'=>'test']);
        $users[] = new User(['email' => 'larissa3@test.com','name' => 'Test3', 'password'=>'test']);
        $list = new UsersList;
        $list->push(...$users);
        $this->assertEquals(2, $list->count());
        $list->offsetUnset(0);
        $this->assertEquals(1, $list->count());
    }

    public function testAdd()
    {
        $user = new User(['email' => 'larissa2@test.com','name' => 'Test1', 'password'=>'test']);
        $list = new UsersList;
        $list->add($user);
        $this->assertEquals(1, $list->count());
    }
}
