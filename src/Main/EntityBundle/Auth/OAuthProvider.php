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
            ->where('u.googleId = :gid OR u.instagramId = :iid')
            ->setParameter('gid', $username)
            ->setParameter('iid', $username)
            ->setMaxResults(1);
    $result = $qb->getQuery()->getResult();

    if (count($result)) {
      return $result[0];
    } else {
      return new User();
    }
  }

  public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
    /* @var $r \HWI\Bundle\OAuthBundle\OAuth\ResponseInterface */
    /* @var $ro \HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface */
    $r = $response->getResponse();
    $ro = $response->getResourceOwner();
    if ($ro instanceof \HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\InstagramResourceOwner && !empty($r)) {
      $code = 200;
      if (isset($r['code'])) {
        $code = $r['code'];
      } else {
        $code = $r['meta']['code'];
      }

      switch ($code) {
        case 403: //INSTAGRAM ERROR TYPE 'OAuthForbiddenException'
          throw new \Exception($r['error_message'], $r['code']);
          break;
      }
    }

    $google_id = 0;
    $instagram_id = 0;
    $instagram_auth_code = 0;
    switch ($response->getResourceOwner()->getName()) {
      case 'google':
        $email = $response->getEmail();
        $google_id = $response->getUsername();

        break;
      case 'instagram':
        $instagram_id = $response->getUsername();
        if (is_null($response->getEmail())) {
          $email = $response->getNickname() . '@instagram.com';
        }
        $instagram_auth_code = $response->getAccessToken();
        break;
    }

    $nickname = $response->getNickname();
    $realname = $response->getRealName();
    $avatar = $response->getProfilePicture();

    //set data in session
    $this->session->set('email', $email);
    $this->session->set('nickname', $nickname);
    $this->session->set('realname', $realname);
    $this->session->set('avatar', $avatar);
    $this->session->set('provider-name', $response->getResourceOwner()->getName());

    //Check if this Google user already exists in our app DB
    $qb = $this->doctrine->getManager()->createQueryBuilder();
    $qb->select('u')
            ->from('MainEntityBundle:User', 'u')
            ->where('u.googleId = :gid OR u.instagramId = :iid ')
            ->setParameter('gid', $google_id)
            ->setParameter('iid', $instagram_id)
            ->setMaxResults(1);
    $result = $qb->getQuery()->getResult();

    //add to database if doesn't exists
    $em = $this->doctrine->getManager();
    if (!count($result)) {
      $user = new User();
      $user->setUsername($realname);
      $user->setRealname($realname);
      $user->setNickname($nickname);
      $user->setEmail($email);
      switch ($response->getResourceOwner()->getName()) {
        case 'google':
          $user->setGoogleId($google_id);
          break;
        case 'instagram':
          $user->setInstagramId($instagram_id);
          break;
      }
      $user->setAvatar($avatar);

      $role = $this->doctrine->getRepository('MainEntityBundle:Role')->findOneByRole("ROLE_USER");

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
    } else {
      $user = $result[0]; /* return User */
      $user->setInstagramAuthCode($instagram_auth_code);
      $em->persist($user);
    }
    $em->flush();

    //set id
    $this->session->set('id', $user->getId());

    return $this->loadUserByUsername($response->getUsername());
  }

  public function supportsClass($class) {
    return $class === 'Main\\EntityBundle\\Entity\\User';
  }

}
