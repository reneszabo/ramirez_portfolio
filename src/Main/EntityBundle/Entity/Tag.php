<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Tag {

  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="name", type="string", length=32, nullable=false)
   */
  protected $name;

  /**
   * @Gedmo\Slug(fields={"name"})
   * @ORM\Column(name="slug" , type="string", length=64, unique=true)
   */
  private $slug;

  /**
   * @var \Doctrine\Common\Collections\Collection
   *
   * @ORM\ManyToMany(targetEntity="Post", mappedBy="tags", cascade={"persist"})
   */
  protected $posts;

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

  public function __toString() {
    return $this->getName();
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
   * Constructor
   */
  public function __construct() {
    $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Set name
   *
   * @param string $name
   * @return Tag
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string 
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set slug
   *
   * @param string $slug
   * @return Tag
   */
  public function setSlug($slug) {
    $this->slug = $slug;

    return $this;
  }

  /**
   * Get slug
   *
   * @return string 
   */
  public function getSlug() {
    return $this->slug;
  }

  /**
   * Add posts
   *
   * @param \Main\EntityBundle\Entity\Post $posts
   * @return Tag
   */
  public function addPost(\Main\EntityBundle\Entity\Post $posts) {
    if (!$this->posts->contains($posts)) {
      $posts->addTag($this);
      $this->posts[] = $posts;
    }
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
