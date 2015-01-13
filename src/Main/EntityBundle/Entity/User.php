<?php
namespace Main\EntityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Acme\UserBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable {

  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=25, unique=true)
   */
  private $username;

  /**
   * @ORM\Column(type="string", length=64)
   */
  private $password;

  /**
   * @ORM\Column(type="string", length=60, unique=true)
   */
  private $email;

  /**
   * @ORM\Column(name="is_active", type="boolean")
   */
  private $isActive;

  /**
   * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
   *
   */
  private $roles;

  public function __construct() {
    $this->isActive = true;
    $this->roles = new ArrayCollection();
    // may not be needed, see section on salt below
    // $this->salt = md5(uniqid(null, true));
  }

  /**
   * @inheritDoc
   */
  public function getUsername() {
    return $this->username;
  }

  /**
   * @inheritDoc
   */
  public function getSalt() {
    // you *may* need a real salt depending on your encoder
    // see section on salt below
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * @inheritDoc
   */
  public function eraseCredentials() {
    
  }

  /**
   * @see \Serializable::serialize()
   */
  public function serialize() {
    return serialize(array(
        $this->id,
        $this->username,
        $this->password,
            // see section on salt below
            // $this->salt,
    ));
  }

  /**
   * @see \Serializable::unserialize()
   */
  public function unserialize($serialized) {
    list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
  }

  public function getRoles() {
    return $this->roles->toArray();
  }

  public function isAccountNonExpired() {
    return true;
  }

  public function isAccountNonLocked() {
    return true;
  }

  public function isCredentialsNonExpired() {
    return true;
  }

  public function isEnabled() {
    return $this->isActive;
  }

}
