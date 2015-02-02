<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Phone
 *
 * @ORM\Table(name="post")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Post {

  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="string", length=255, nullable=false)
   */
  protected $title;

  /**
   * @Gedmo\Slug(fields={"title"})
   * @ORM\Column(name="slug" , type="string", length=255, unique=true)
   */
  private $slug;

  /**
   * @ORM\OneToOne(targetEntity="Image")
   * @ORM\JoinColumn(name="iamge_id", referencedColumnName="id", nullable=true)
   */
  protected $image;

  /**
   * @ORM\Column(name="content", type="text", nullable=true)
   */
  protected $content;

  /**
   * @ORM\ManyToOne(targetEntity="User",  inversedBy="posts", cascade={"persist"}) 
   * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
   */
  protected $author;

  /**
   * @var \Doctrine\Common\Collections\Collection
   * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts")
   */
  private $tags;

  /**
   * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
   * @ORM\OrderBy({"createdAt" = "ASC"})
   */
  private $comments;

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

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set title
   *
   * @param string $title
   * @return Post
   */
  public function setTitle($title) {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   *
   * @return string 
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Set content
   *
   * @param string $content
   * @return Post
   */
  public function setContent($content) {
    $this->content = $content;

    return $this;
  }

  /**
   * Get content
   *
   * @return string 
   */
  public function getContent($read_more = false) {
    if ($read_more === true) {
      $split_text = explode("[split]", $this->content, 2);
      return $split_text[0];
    }
    return str_replace('[split]', '', $this->content);
  }

  /**
   * Set author
   *
   * @param \Main\EntityBundle\Entity\User $author
   * @return Post
   */
  public function setAuthor(\Main\EntityBundle\Entity\User $author = null) {
    $this->author = $author;

    return $this;
  }

  /**
   * Get author
   *
   * @return \Main\EntityBundle\Entity\User 
   */
  public function getAuthor() {
    return $this->author;
  }

  /**
   * Set slug
   *
   * @param string $slug
   * @return Post
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
   * Set image
   *
   * @param \Main\EntityBundle\Entity\Image $image
   * @return Post
   */
  public function setImage(\Main\EntityBundle\Entity\Image $image = null) {
    $this->image = $image;

    return $this;
  }

  /**
   * Get image
   *
   * @return \Main\EntityBundle\Entity\Image 
   */
  public function getImage() {
    return $this->image;
  }

  /**
   * Constructor
   */
  public function __construct() {
    $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Add tags
   *
   * @param \Main\EntityBundle\Entity\Tag $tags
   * @return Post
   */
  public function addTag(\Main\EntityBundle\Entity\Tag $tags) {
    if (!$this->tags->contains($tags)) {
      $tags->addPost($this);
      $this->tags[] = $tags;
    }
    return $this;
  }

  /**
   * Remove tags
   *
   * @param \Main\EntityBundle\Entity\Tag $tags
   */
  public function removeTag(\Main\EntityBundle\Entity\Tag $tags) {
    $this->tags->removeElement($tags);
  }

  /**
   * Get tags
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getTags() {
    return $this->tags;
  }

  /**
   * Add comments
   *
   * @param \Main\EntityBundle\Entity\Comment $comments
   * @return Post
   */
  public function addComment(\Main\EntityBundle\Entity\Comment $comments) {
    $this->comments[] = $comments;

    return $this;
  }

  /**
   * Remove comments
   *
   * @param \Main\EntityBundle\Entity\Comment $comments
   */
  public function removeComment(\Main\EntityBundle\Entity\Comment $comments) {
    $this->comments->removeElement($comments);
  }

  /**
   * Get comments
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getComments() {
    return $this->comments;
  }

}
