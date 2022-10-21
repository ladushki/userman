<?php declare(strict_types = 1);

namespace Volga\Userman\Models;

use Volga\Userman\Contracts\UserInterface;

class User implements UserInterface
{
    /**
     * @var int|null
     */
    private ?int $id = null;
    /**
     * @var string|null
     */
    private ?string $name;
    /**
     * @var string
     */
    private string $email;
    /**
     * @var string|null
     */
    private ?string $surname = null;

    /**
     * @var string|null
     */
    private ?string $password;

    /**
     * @var array|string[]
     */
    private array $fillable = ['email', 'name', 'surname', 'password'];

    /**
     * @param array $userData
     */
    public function __construct(array $userData = [])
    {
        $this->setFromArray($userData);
    }

    /**
     * @param object $dataObject
     * @return UserInterface
     */
    public function createFromDataObject(object $dataObject): UserInterface
    {
        if (empty($dataObject->email) || empty($dataObject->password)) {
            throw new \InvalidArgumentException('Password and email are required');
        }

        $this->id = $dataObject->id;
        $this->setEmail($dataObject->email);
        $this->setName($dataObject->name);
        $this->setSurname($dataObject->surname);
        $this->setPassword($dataObject->password);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname
     */
    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param array $userData
     * @return UserInterface
     */
    private function setFromArray(array $userData): UserInterface
    {
        $attributes = [];

        if (count($this->getFillable()) > 0) {
            $attributes = array_intersect_key($userData, array_flip($this->getFillable()));
        }

        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * @return array|string[]
     */
    private function getFillable()
    {
        return $this->fillable;
    }
}
