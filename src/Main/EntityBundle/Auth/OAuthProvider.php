<?php

namespace Main\EntityBundle\Auth;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Main\EntityBundle\Entity\User;

class OAuthProvider extends OAuthUserProvider {

  protected $session, $doctrine, $admins;

  public function __construct($session, $doctrine, $service_container) {
    $this->session = $session;
    $this->doctrine = $doctrine;
    $this->container = $service_container;
  }

  public function loadUserByUsername($username) {

    $qb = $this->doctrine->getManager()->createQueryBuilder();
    $qb->select('u')
            ->from('MainEntityBundle:User', 'u')
            ->where('u.googleId = :gid')
            ->setParameter('gid', $username)
            ->setMaxResults(1);
    $result = $qb->getQuery()->getResult();

    if (count($result)) {
      return $result[0];
    } else {
      return new User();
    }
  }

  public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
    //Data from Google response
    $google_id = $response->getUsername();
    $email = $response->getEmail();
    $nickname = $response->getNickname();
    $realname = $response->getRealName();
    $avatar = $response->getProfilePicture();

    //set data in session
    $this->session->set('email', $email);
    $this->session->set('nickname', $nickname);
    $this->session->set('realname', $realname);
    $this->session->set('avatar', $avatar);

    //Check if this Google user already exists in our app DB
    $qb = $this->doctrine->getManager()->createQueryBuilder();
    $qb->select('u')
            ->from('MainEntityBundle:User', 'u')
            ->where('u.googleId = :gid')
            ->setParameter('gid', $google_id)
            ->setMaxResults(1);
    $result = $qb->getQuery()->getResult();

    //add to database if doesn't exists
    if (!count($result)) {
      $user = new User();
      $user->setUsername($google_id);
      $user->setRealname($realname);
      $user->setNickname($nickname);
      $user->setEmail($email);
      $user->setGoogleId($google_id);
      $user->setAvatar($avatar);

      $role = $this->doctrine->getRepository('MainEntityBundle:Role')->findOneByRole("ROLE_USER");
      $em = $this->doctrine->getManager();
      if (!$role) {
        $role = new \Main\EntityBundle\Entity\Role();
        $role->setName("User");
        $role->setRole("ROLE_USER");
        $em->persist($role);
      }
      //Set some wild random pass since its irrelevant, this is Google login
      $factory = $this->container->get('security.encoder_factory');
      $encoder = $factory->getEncoder($user);
      $password = $encoder->encodePassword(md5(uniqid()), $user->getSalt());
      $user->setPassword($password);
      $user->addRole($role);
      $em->persist($user);
      $em->flush();
    } else {
      $user = $result[0]; /* return User */
    }

    //set id
    $this->session->set('id', $user->getId());

    return $this->loadUserByUsername($response->getUsername());
  }

  public function supportsClass($class) {
    return $class === 'Main\\EntityBundle\\Entity\\User';
  }

}
