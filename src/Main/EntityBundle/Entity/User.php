<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Acme\UserBundle\Entity\UserRepository")
 * @UniqueEntity(fields="username", message="Username must be unique")
 * @UniqueEntity(fields="email", message="Email must be unique")
 */
class User extends OAuthUser implements EquatableInterface, \Serializable {

  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(name="username", type="string", length=255, unique=true)
   */
  protected $username;

  /**
   * @var string
   *
   * @ORM\Column(name="realname", type="string", length=255, nullable=true)
   */
  protected $realname;

  /**
   * @var string
   *
   * @ORM\Column(name="nickname", type="string", length=255, nullable=true)
   */
  protected $nickname;

  /**
   * @var string
   *
   * @ORM\Column(name="salt", type="string", length=32)
   */
  protected $salt;

  /**
   * @var string
   *
   * @ORM\Column(name="password", type="string", length=255)
   */
  protected $password;

  /**
   * @var string
   *
   * @ORM\Column(name="email", type="string", length=255, unique=true)
   */
  protected $email;

  /**
   * @ORM\Column(name="is_active", type="boolean")
   */
  protected $isActive;

  /**
   * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
   *
   */
  protected $roles;

  /**
   * @var string
   *
   * @ORM\Column(name="google_id", type="string", length=255, unique=true, nullable=true)
   */
  protected $googleId;

  /**
   * @var string
   *
   * @ORM\Column(name="avatar", type="string", nullable=true)
   */
  protected $avatar;

  /**
   * @ORM\OneToMany(targetEntity="Post", mappedBy="author")
   * @ORM\OrderBy({"createdAt" = "ASC"})
   */
  private $posts;

  public function __construct() {
    $this->isActive = true;
    $this->roles = new ArrayCollection();
    $this->posts = new ArrayCollection();
    $this->salt = md5(uniqid(null, true));
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
    ));
  }

  /**
   * @see \Serializable::unserialize()
   */
  public function unserialize($serialized) {
    list (
            $this->id,
            $this->username,
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

  public function isEqualTo(UserInterface $user) {
    if ((int) $this->getId() === $user->getId()) {
      return true;
    }

    return false;
  }

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set username
   *
   * @param string $username
   * @return User
   */
  public function setUsername($username) {
    $this->username = $username;

    return $this;
  }

  /**
   * Set realname
   *
   * @param string $realname
   * @return User
   */
  public function setRealname($realname) {
    $this->realname = $realname;

    return $this;
  }

  /**
   * Get realname
   *
   * @return string 
   */
  public function getRealname() {
    return $this->realname;
  }

  /**
   * Set nickname
   *
   * @param string $nickname
   * @return User
   */
  public function setNickname($nickname) {
    $this->nickname = $nickname;

    return $this;
  }

  /**
   * Get nickname
   *
   * @return string 
   */
  public function getNickname() {
    return $this->nickname;
  }

  /**
   * Set salt
   *
   * @param string $salt
   * @return User
   */
  public function setSalt($salt) {
    $this->salt = $salt;

    return $this;
  }

  /**
   * Set password
   *
   * @param string $password
   * @return User
   */
  public function setPassword($password) {
    $this->password = $password;

    return $this;
  }

  /**
   * Set email
   *
   * @param string $email
   * @return User
   */
  public function setEmail($email) {
    $this->email = $email;

    return $this;
  }

  /**
   * Get email
   *
   * @return string 
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * Set isActive
   *
   * @param boolean $isActive
   * @return User
   */
  public function setIsActive($isActive) {
    $this->isActive = $isActive;

    return $this;
  }

  /**
   * Get isActive
   *
   * @return boolean 
   */
  public function getIsActive() {
    return $this->isActive;
  }

  /**
   * Set googleId
   *
   * @param string $googleId
   * @return User
   */
  public function setGoogleId($googleId) {
    $this->googleId = $googleId;

    return $this;
  }

  /**
   * Get googleId
   *
   * @return string 
   */
  public function getGoogleId() {
    return $this->googleId;
  }

  /**
   * Set avatar
   *
   * @param string $avatar
   * @return User
   */
  public function setAvatar($avatar) {
    $this->avatar = $avatar;

    return $this;
  }

  /**
   * Get avatar
   *
   * @return string 
   */
  public function getAvatar() {
    return $this->avatar;
  }

  /**
   * Add roles
   *
   * @param \Main\EntityBundle\Entity\Role $roles
   * @return User
   */
  public function addRole(\Main\EntityBundle\Entity\Role $roles) {
    $this->roles[] = $roles;

    return $this;
  }

  /**
   * Remove roles
   *
   * @param \Main\EntityBundle\Entity\Role $roles
   */
  public function removeRole(\Main\EntityBundle\Entity\Role $roles) {
    $this->roles->removeElement($roles);
  }

  /**
   * Add posts
   *
   * @param \Main\EntityBundle\Entity\Post $posts
   * @return User
   */
  public function addPost(\Main\EntityBundle\Entity\Post $posts) {
    $this->posts[] = $posts;

    return $this;
  }

  /**
   * Remove posts
   *
   * @param \Main\EntityBundle\Entity\Post $posts
   */
  public function removePost(\Main\EntityBundle\Entity\Post $posts) {
    $this->posts->removeElement($posts);
  }

  /**
   * Get posts
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getPosts() {
    return $this->posts;
  }

}
