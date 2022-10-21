<?php declare(strict_types = 1);

namespace Volga\Userman\Repositories;

use Illuminate\Database\ConnectionInterface;
use InvalidArgumentException;
use Volga\Userman\Contracts\PasswordHashingInterface;
use Volga\Userman\Contracts\UserInterface;
use Volga\Userman\Contracts\UserRepositoryInterface;
use Volga\Userman\Exceptions\UserNotFoundException;
use Volga\Userman\Models\User;
use Volga\Userman\Models\UsersList;

/**
 *
 */
class IlluminateUserRepository implements UserRepositoryInterface
{

    use ValidatesBeforePersist;

    /**
     * @var string
     */
    private string $table = 'users';
    /**
     * @var ConnectionInterface
     */
    private ConnectionInterface $connection;

    /**
     * @var PasswordHashingInterface
     */
    private PasswordHashingInterface $hasher;

    /**
     * Create a new database user provider.
     *
     * @param ConnectionInterface      $connection
     * @param PasswordHashingInterface $hasher
     */
    public function __construct(ConnectionInterface $connection, PasswordHashingInterface $hasher)
    {
        $this->connection = $connection;
        $this->hasher = $hasher;
    }

    /**
     * @param array $input
     * @return void
     * @throws \Volga\Userman\Exceptions\PasswordHashException
     */
    final public function insertReturnId(array $input): ?int
    {
        $validated = array_filter([
            'name' => $input['name'],
            'surname' => $input['surname'] ?? null,
            'email' => $input['email'],
            'password' => $this->hasher->hash($input['password'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->connection->table($this->table)->insertGetId($validated);
    }

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    final public function getById(int $id): User
    {
        $user = new User();
        $data = $this->connection->table($this->table)
            ->select()->where('id', '=', $id)
            ->first();

        if (!($data)) {
            throw new UserNotFoundException('User not found');
        }

        return $user->createFromDataObject($data);
    }

    /**
     * @param string $identifier Like email.
     * @return UserInterface
     * @throws UserNotFoundException
     */
    final public function getByIdentifier(string $identifier): UserInterface
    {
        $user = new User();

        $data = $this->connection->table($this->table)
            ->select()->where('email', '=', $identifier)
            ->first();

        if (!($data)) {
            throw new UserNotFoundException('User not found');
        }

        return $user->createFromDataObject($data);
    }

    /**
     * {@inheritdoc}
     */
    final public function update(UserInterface $user, array $attributes): UserInterface
    {
        $validated = array_filter([
            'email' => $attributes['email'] ?? null,
            'name' => $attributes['name'] ?? null,
            'surname' => $attributes['surname'] ?? null,
            'password' => $this->hasher->hash($attributes['password'] ?? null),
        ]);

        if (empty($validated)) {
            throw new InvalidArgumentException('Nothing to update');
        }

        if ($user->getPassword() === $validated['password']) {
            throw new InvalidArgumentException('Nothing to update.');
        }

        $this->connection->table($this->table)->where('id', '=', $user->getId())->update($validated);

        return $user;
    }

    /**
     * @return UsersList
     */
    final public function getList(): UsersList
    {
        $users = $this->connection->table($this->table)->select()->get();

        return new UsersList($users->toArray());
    }
}
