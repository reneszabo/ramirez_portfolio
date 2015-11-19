<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * InstagramImage
 * @ORM\Table(name="instagram_image")
 * @ORM\Entity(repositoryClass="Main\EntityBundle\Entity\InstagramImageRepository")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("None")
 */
class InstagramImage {

  /**
   * @ORM\Id
   * @ORM\Column(name="id", type="string", nullable=false)
   */
  private $id;

  /**
   * @ORM\Column(name="attribution", type="string", length=100, nullable=true)
   */
  protected $attribution;

  /**
   * @ORM\Column(name="tags", type="json_array", nullable=true)
   */
  protected $tags;

  /**
   * @ORM\Column(name="type", type="string", length=10 , nullable=false)
   */
  protected $type;

  /**
   * @ORM\Column(name="location", type="json_array", nullable=true)
   */
  protected $location;

  /**
   * @ORM\Column(name="comments", type="json_array", nullable=true)
   */
  protected $comments;

  /**
   * @ORM\Column(name="filter", type="string", length=25 , nullable=false)
   */
  protected $filter;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="created_time", type="integer", nullable=false)
   */
  protected $createdTime;

  /**
   * @ORM\Column(name="link", type="string", length=255 , nullable=false)
   */
  protected $link;

  /**
   * @ORM\Column(name="likes", type="json_array", nullable=false)
   */
  protected $likes;

  /**
   * @ORM\Column(name="images", type="json_array", nullable=false)
   */
  protected $images;

  /**
   * @ORM\Column(name="users_in_photo", type="json_array", nullable=true)
   */
  protected $usersInPhoto;

  /**
   * @ORM\Column(name="caption", type="json_array", nullable=true)
   */
  protected $caption;

  /**
   * @ORM\Column(name="user_has_liked", type="boolean", nullable=true)
   */
  protected $userHasLiked;

  /**
   * @ORM\Column(name="user", type="json_array", nullable=true)
   */
  protected $user;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="created_at", type="datetime", nullable=false)
   * @JMS\Exclude()
   */
  protected $createdAt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="updated_at", type="datetime", nullable=true)
   * @JMS\Exclude()
   */
  protected $updatedAt;

  public function __toString() {
    return $this->getId();
  }

  /**
   * Constructor
   */
  public function __construct() {
    
  }

  /**
   * @ORM\PrePersist()
   * 
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return Tag
   */
  public function setCreatedAt($createdAt = null) {
    if (!($createdAt instanceof \DateTime) && !(is_string($createdAt)) && !(is_int($createdAt) || is_integer($createdAt))) {
      $createdAt = null;
    }
    $this->createdAt = null === $createdAt ? new \DateTime() : $createdAt;

    return $this;
  }

  /**
   * Get createdAt
   *
   * @return \DateTime 
   */
  public function getCreatedAt() {
    return $this->createdAt;
  }

  /**
   * @ORM\PrePersist()
   * @ORM\PreUpdate()
   * 
   * Set updatedAt
   *
   * @param \DateTime $updatedAt
   * @return Tag
   */
  public function setUpdatedAt($updatedAt = null) {
    if (!($updatedAt instanceof \DateTime) && !(is_string($updatedAt)) && !(is_int($updatedAt) || is_integer($updatedAt))) {
      $updatedAt = null;
    }
    $this->updatedAt = null === $updatedAt ? new \DateTime() : $updatedAt;

    return $this;
  }

  /**
   * Get updatedAt
   *
   * @return \DateTime 
   */
  public function getUpdatedAt() {
    return $this->updatedAt;
  }

  /**
   * Set id
   *
   * @param string $id
   *
   * @return InstagramImage
   */
  public function setId($id) {
    $this->id = $id;

    return $this;
  }

  /**
   * Get id
   *
   * @return string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set attribution
   *
   * @param string $attribution
   *
   * @return InstagramImage
   */
  public function setAttribution($attribution) {
    $this->attribution = $attribution;

    return $this;
  }

  /**
   * Get attribution
   *
   * @return string
   */
  public function getAttribution() {
    return $this->attribution;
  }

  /**
   * Set tags
   *
   * @param array $tags
   *
   * @return InstagramImage
   */
  public function setTags($tags) {
    $this->tags = $tags;

    return $this;
  }

  /**
   * Get tags
   *
   * @return array
   */
  public function getTags() {
    return $this->tags;
  }

  /**
   * Set type
   *
   * @param string $type
   *
   * @return InstagramImage
   */
  public function setType($type) {
    $this->type = $type;

    return $this;
  }

  /**
   * Get type
   *
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Set location
   *
   * @param array $location
   *
   * @return InstagramImage
   */
  public function setLocation($location) {
    $this->location = $location;

    return $this;
  }

  /**
   * Get location
   *
   * @return array
   */
  public function getLocation() {
    return $this->location;
  }

  /**
   * Set comments
   *
   * @param array $comments
   *
   * @return InstagramImage
   */
  public function setComments($comments) {
    $this->comments = $comments;

    return $this;
  }

  /**
   * Get comments
   *
   * @return array
   */
  public function getComments() {
    return $this->comments;
  }

  /**
   * Set filter
   *
   * @param string $filter
   *
   * @return InstagramImage
   */
  public function setFilter($filter) {
    $this->filter = $filter;

    return $this;
  }

  /**
   * Get filter
   *
   * @return string
   */
  public function getFilter() {
    return $this->filter;
  }

  /**
   * Set createdTime
   *
   * @param \DateTime $createdTime
   *
   * @return InstagramImage
   */
  public function setCreatedTime($createdTime) {
    $this->createdTime = $createdTime;

    return $this;
  }

  /**
   * Get createdTime
   *
   * @return \DateTime
   */
  public function getCreatedTime() {
    return $this->createdTime;
  }

  /**
   * Set link
   *
   * @param string $link
   *
   * @return InstagramImage
   */
  public function setLink($link) {
    $this->link = $link;

    return $this;
  }

  /**
   * Get link
   *
   * @return string
   */
  public function getLink() {
    return $this->link;
  }

  /**
   * Set likes
   *
   * @param array $likes
   *
   * @return InstagramImage
   */
  public function setLikes($likes) {
    $this->likes = $likes;

    return $this;
  }

  /**
   * Get likes
   *
   * @return array
   */
  public function getLikes() {
    return $this->likes;
  }

  /**
   * Set images
   *
   * @param array $images
   *
   * @return InstagramImage
   */
  public function setImages($images) {
    $this->images = $images;

    return $this;
  }

  /**
   * Get images
   *
   * @return array
   */
  public function getImages() {
    return $this->images;
  }

  /**
   * Set usersInPhoto
   *
   * @param array $usersInPhoto
   *
   * @return InstagramImage
   */
  public function setUsersInPhoto($usersInPhoto) {
    $this->usersInPhoto = $usersInPhoto;

    return $this;
  }

  /**
   * Get usersInPhoto
   *
   * @return array
   */
  public function getUsersInPhoto() {
    return $this->usersInPhoto;
  }

  /**
   * Set caption
   *
   * @param array $caption
   *
   * @return InstagramImage
   */
  public function setCaption($caption) {
    $this->caption = $caption;

    return $this;
  }

  /**
   * Get caption
   *
   * @return array
   */
  public function getCaption() {
    return $this->caption;
  }

  /**
   * Set userHasLiked
   *
   * @param boolean $userHasLiked
   *
   * @return InstagramImage
   */
  public function setUserHasLiked($userHasLiked) {
    $this->userHasLiked = $userHasLiked;

    return $this;
  }

  /**
   * Get userHasLiked
   *
   * @return boolean
   */
  public function getUserHasLiked() {
    return $this->userHasLiked;
  }

  /**
   * Set user
   *
   * @param array $user
   *
   * @return InstagramImage
   */
  public function setUser($user) {
    $this->user = $user;

    return $this;
  }

  /**
   * Get user
   *
   * @return array
   */
  public function getUser() {
    return $this->user;
  }

  public function updateAll(InstagramImage $obj) {
    $this->setAttribution($obj->getAttribution());
    $this->setTags($obj->getTags());
    $this->setLocation($obj->getLocation());
    $this->setComments($this->getComments());
    $this->setLikes($this->getLikes());
    $this->setUsersInPhoto($this->getUsersInPhoto());
    $this->setCaption($this->getCaption());
    $this->setUserHasLiked($this->getUserHasLiked());
  }

}
