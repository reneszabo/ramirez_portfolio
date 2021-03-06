<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="game")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Game {
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="score", type="text", nullable=false)
   */
  private $score;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
   */
  private $user;

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
   * Set user
   *
   * @param \Main\EntityBundle\Entity\User $user
   * @return Game
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
     * Set score
     *
     * @param string $score
     *
     * @return Game
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return string
     */
    public function getScore()
    {
        return $this->score;
    }
}
