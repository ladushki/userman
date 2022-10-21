<?php declare(strict_types = 1);

namespace Volga\Userman\Models;

use Volga\Userman\Contracts\UserInterface;

class UsersList implements \Countable, \ArrayAccess
{

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $this->collect($data);
    }

    /**
     * @param   ...$values
     * @return $this
     */
    public function push(...$values)
    {
        foreach ($values as $value) {
            $this->data[] = $value;
        }

        return $this;
    }

    public function add(UserInterface $user): UsersList
    {
        $this->data[] = $user;

        return $this;
    }

    /**
     * @return integer
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->data ?? [];
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet(mixed $offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        if (is_null($key)) {
            $this->data[] = $value;
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * @param array $collection
     * @return array
     */
    private function collect(array $collection): array
    {
        $data = [];

        foreach ($collection as $userData) {
            if (is_object($userData)) {
                $data[] = ((new User())->createFromDataObject($userData));
            }

            if (is_array($userData)) {
                $data[] = new User($userData);
            }
        }

        return $data;
    }
}
