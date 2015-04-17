<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class File {

  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="path", type="string", length=255, nullable=false)
   */
  private $path;

  /**
   * @ORM\Column(name="order_like", type="integer", nullable=true)
   */
  private $orderLike;

  /**
   * @ORM\ManyToOne(targetEntity="Post",  inversedBy="files") 
   * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
   */
  protected $post;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="created_at", type="datetime", nullable=false)
   */
  protected $createdAt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="updated_at", type="datetime", nullable=true)
   */
  protected $updatedAt;

  public function __construct() {
    
  }

  public function __toString() {
    return $this->path;
  }

  /**
   * @ORM\PrePersist()
   * 
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return File
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
   * @return File
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
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set path
   *
   * @param string $path
   * @return File
   */
  public function setPath($path) {
    $this->path = $path;

    return $this;
  }

  /**
   * Get path
   *
   * @return string 
   */
  public function getPath() {
    if ($this->path[0] != "/") {
      return "/" . $this->path;
    }
    return $this->path;
  }

  /**
   * Set post
   *
   * @param \Main\EntityBundle\Entity\Post $post
   * @return File
   */
  public function setPost(\Main\EntityBundle\Entity\Post $post = null) {
    if (!$this->post) {
      $this->post = $post;
      $post->addFile($this);
    }

    return $this;
  }

  /**
   * Get post
   *
   * @return \Main\EntityBundle\Entity\Post 
   */
  public function getPost() {
    return $this->post;
  }

  /**
   * Set orderLike
   *
   * @param integer $orderLike
   * @return File
   */
  public function setOrderLike($orderLike) {
    $this->orderLike = $orderLike;

    return $this;
  }

  /**
   * Get orderLike
   *
   * @return integer 
   */
  public function getOrderLike() {
    return $this->orderLike;
  }

}
