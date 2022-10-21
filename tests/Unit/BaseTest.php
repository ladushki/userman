<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;

class BaseTest extends TestCase
{
    protected Capsule $capsule;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->capsule = new Capsule;

        $this->capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => 'true',
        ]);

        $this->capsule->setAsGlobal();

        Capsule::schema('default')->create('users', function ($table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('name', 255)->nullable();
            $table->string('surname', 255)->nullable();
            $table->string('password', 255)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function testCanConnectToDb(): void
    {
        Capsule::table('users')->insertGetId([
            'name' => 'Larissa', 'password' => 'larissa', 'email' => 'larissa@test.com',
        ]);
        $count = Capsule::table('users')->where('id', '>', 0)->count();
        $this->assertEquals(1, $count);
    }

}
