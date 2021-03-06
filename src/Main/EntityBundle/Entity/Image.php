<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="Main\EntityBundle\Entity\ImageRepository")
 * @Gedmo\Uploadable(path="img/post/img", callback="myCallbackMethod", filenameGenerator="SHA1", allowOverwrite=true, appendNumber=true)
 * @ORM\HasLifecycleCallbacks()
 */
class Image {

  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="path", type="string")
   * @Gedmo\UploadableFilePath
   */
  private $path;

  /**
   * @ORM\Column(name="alt", type="string", length=255 , nullable=true)
   */
  private $alt;

  /**
   * @ORM\Column(name="name", type="string")
   * @Gedmo\UploadableFileName
   */
  private $name;

  /**
   * @ORM\Column(name="mime_type", type="string")
   * @Gedmo\UploadableFileMimeType
   */
  private $mimeType;

  /**
   * @ORM\Column(name="size", type="decimal")
   * @Gedmo\UploadableFileSize
   */
  private $size;

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
    $this->alt = "Rene Ramirez - BLOG";
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
   * @return Analytics
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

  public function myCallbackMethod(array $info) {
    // Do some stuff with the file..
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
   * @return Image
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
   * Set name
   *
   * @param string $name
   * @return Image
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
   * Set mimeType
   *
   * @param string $mimeType
   * @return Image
   */
  public function setMimeType($mimeType) {
    $this->mimeType = $mimeType;

    return $this;
  }

  /**
   * Get mimeType
   *
   * @return string 
   */
  public function getMimeType() {
    return $this->mimeType;
  }

  /**
   * Set size
   *
   * @param string $size
   * @return Image
   */
  public function setSize($size) {
    $this->size = $size;

    return $this;
  }

  /**
   * Get size
   *
   * @return string 
   */
  public function getSize() {
    return $this->size;
  }

  /**
   * Set alt
   *
   * @param string $alt
   * @return Image
   */
  public function setAlt($alt) {
    $this->alt = $alt;

    return $this;
  }

  /**
   * Get alt
   *
   * @return string 
   */
  public function getAlt() {
    return $this->alt;
  }

}
