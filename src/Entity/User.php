<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="username",
 *          column=@ORM\Column(
 *              name     = "username",
 *              length   = 100,
 *              unique   = true,
 *          )
 *      )
 * })
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="username", type="string")
     * @ORM\Id
     */
    protected $username;

    /**
     * @var array
     *
     * @ORM\Column(name="settings", type="json")
     */
    protected $settings = [];

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return User
     */
    public function setSettings(array $settings): User
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @param mixed $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getRoles()
    {
        return ['user'];
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

}