<?php declare(strict_types = 1);

namespace Volga\Userman\Contracts;

interface UserInterface
{

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @return string|null
     */
    public function getSurname(): ?string;
}
