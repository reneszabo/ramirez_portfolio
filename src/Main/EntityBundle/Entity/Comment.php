<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="comment")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Comment {

  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="comment", type="text", nullable=true)
   */
  private $comment;

  /**
   * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
   */
  private $user;

  /**
   * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
   * @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
   */
  private $post;

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
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set comment
   *
   * @param string $comment
   * @return Comment
   */
  public function setComment($comment, $admin = false) {
    if ($admin == true) {
      $this->comment = $comment;
    } else {
      $comment = str_replace('<javascrip', 'XXX', $comment);
      $comment = str_replace('<iframe', 'XXX', $comment);
      $comment = str_replace('href', 'XXX', $comment);
      $comment = str_replace('src', 'XXX', $comment);
      $this->comment = $comment;
    }

    return $this;
  }

  /**
   * Get comment
   *
   * @return string 
   */
  public function getComment() {
    return $this->comment;
  }

  /**
   * Set user
   *
   * @param \Main\EntityBundle\Entity\User $user
   * @return Comment
   */
  public function setUser(\Main\EntityBundle\Entity\User $user) {
    $this->user = $user;

    return $this;
  }

  /**
   * Get user
   *
   * @return \Main\EntityBundle\Entity\User 
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Set post
   *
   * @param \Main\EntityBundle\Entity\Post $post
   * @return Comment
   */
  public function setPost(\Main\EntityBundle\Entity\Post $post) {
    $this->post = $post;

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

}
