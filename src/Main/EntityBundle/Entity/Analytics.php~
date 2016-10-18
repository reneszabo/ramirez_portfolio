<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Phone
 *
 * @ORM\Table(name="analytics")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Analytics {

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @var integer
   *
   * @ORM\Column(name="sessions", type="integer", nullable=false)
   */
  private $sessions = 1;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="created_at", type="datetime", nullable=false)
   */
  private $createdAt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="updated_at", type="datetime", nullable=true)
   */
  private $updatedAt;

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @ORM\PrePersist()
   * 
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return Analytics
   */
  public function setCreatedAt($createdAt = null) {
    if (!($createdAt instanceof \DateTime)) {
      if (!(is_string($createdAt))) {
        if (!(is_int($createdAt) || is_integer($createdAt))) {
          $createdAt = null;
        }
      }
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
   * @return Analytics
   */
  public function setUpdatedAt($updatedAt = null) {
    if (!($updatedAt instanceof \DateTime)) {
      if (!(is_string($updatedAt))) {
        if (!(is_int($updatedAt) || is_integer($updatedAt))) {
          $updatedAt = null;
        }
      }
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
   * Set sessions
   *
   * @param integer $sessions
   * @return Analytics
   */
  public function setSessions($sessions) {
    $this->sessions = $sessions;

    return $this;
  }

  /**
   * Get sessions
   *
   * @return integer 
   */
  public function getSessions() {
    return $this->sessions;
  }

}
