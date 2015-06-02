<?php

namespace InstagramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Instaphp\Exceptions\Exception;
use InstagramBundle\Adapter\InstagramAdapter;
use ZMQ;
use ZMQContext;
use GuzzleHttp\Exception\RequestException;

class InstagramController extends Controller {

  public function indexAction(Request $request) {
    /* @var $api InstagramAdapter */
    /* @var $user \Main\EntityBundle\Entity\User */
    /* @var $userResponse \Instaphp\Instagram\Response */
    $user = $this->getUser();
    $api = $this->get('instagram');
    $userResponse = $api->Users->Info($user->getInstagramId());

    return $this->render('InstagramBundle:Default:index.html.twig', array('user' => $user, 'userResponse' => $userResponse));
  }

  public function tagAction(Request $request, $query) {
    /* @var $serializer \JMS\Serializer\Serializer */
    /* @var $api InstaphpAdapter */
    /* @var $tags \Instaphp\Instagram\Tags */
    /* @var $em \Doctrine\ORM\EntityManager */
    /* @var $instagramRepository \Main\EntityBundle\Entity\InstagramImageRepository */
    $user = $this->getUser();
    $api = $this->get('instagram');
    $userInfo = $api->Users->Info($user->getInstagramId());
    $tags = $api->Tags;
    try {
      $tagsRecent = $tags->Recent($query);
    } catch (RequestException $ex) {
      $this->get('logger')->info('CURL ERROR');
    }
    $em = $this->getDoctrine()->getManager();
    $instagramRepository = $em->getRepository('Main\EntityBundle\Entity\InstagramImage');
    $this->storeInstagramResponse($tagsRecent, $em, $this->get('jms_serializer'), $instagramRepository, 'tag', $query);
    $tags = $instagramRepository->findByTag($query);
//    $usersRace = $this->countUserImages($tags);
//    var_dump(\DateTime::createFromFormat("d/m/Y h:i:s A", "27/05/2015 08:00:10 AM")->format("U"));

    return $this->render('InstagramBundle:Tag:index.html.twig', array('tagName' => $query, 'tags' => $tags, 'userInfo' => $userInfo));
  }

  private function countUserImages($tags) {
    $aux = array();
    $auxResult = array();
    $auxResultConunt = array();
    foreach ($tags as $tag) {
      $user = $tag->getUser();
      if (!is_null($user)) {
        $aux[$user['username']][] = $tag;
        $auxResult[$user['username']] = array('count' => count($aux[$user['username']]), 'user' => $user);
        $auxResultConunt[$user['username']] = count($aux[$user['username']]);
      }
    }
    array_multisort($auxResultConunt, SORT_DESC, $auxResult);
    return $auxResult;
  }

  public function callbackAction(Request $request) {
    /* @var $api InstagramAdapter */
    $code = $request->get('code');
    if (!empty($code)) {
      $api = $this->get('instaphp');
      try {
        $success = $api->Users->Authorize($code);
        $token = $api->getAccessToken();
        $isLoggedIn = $this->get('instaphp_token_handler')->setToken($token);
        $this->get('session')->getFlashBag()->add('loggedin', 'Thanks for logging in');
        return $this->redirect($this->generateUrl($this->container->getParameter('instaphp.redirect_route_login')));
      } catch (Exception $e) {
        throw $this->createNotFoundException('Invalid Request', $e);
      } catch (\Exception $e) {
        throw $this->createNotFoundException('Error', $e);
      }
    }
    throw $this->createNotFoundException('Invalid Request');
  }

  public function subscription_updateAction(Request $request) {
    /* @var $api InstagramAdapter */
    /* @var $subscriptions \Instaphp\Instagram\Subscriptions  */
    /* @var $response \Instaphp\Instagram\Response  */
    $type = '';
    $query = '';
    $instagramResponse = array();
    $logger = $this->get('logger');
    $logger->info("-----------------------------------------------------------");
    $logger->info("-----------------------------------------------------------");
    $logger->info($request->getMethod());
    if ('POST' === $request->getMethod()) {
      $api = $this->get('instagram');
      $subscriptions = $api->Subscriptions;
      $logger->info($request->getContent());
      try {
        $instagramResponse = $subscriptions->Recieve($request->getContent());
        $this->get('logger')->info($request->getContent());
      } catch (RequestException $ex) {
        $this->get('logger')->info('CURL ERROR');
      }
      foreach ($instagramResponse as $type => $querys) {
        foreach ($querys as $query => $responses) {
          foreach ($responses as $response) {

            $logger->info('TYPE -> ' . $type);
            $logger->info('QUERY -> ' . $query);
            $em = $this->getDoctrine()->getManager();
            $instagramRepository = $em->getRepository('Main\EntityBundle\Entity\InstagramImage');
            $this->storeInstagramResponse($response, $em, $this->get('jms_serializer'), $instagramRepository, $type, $query);
          }
        }
      }
    }
    $token = $request->get('hub_challenge', "Rene <3 API");
    $responseBack = new Response($token, Response::HTTP_OK);
    $responseBack->headers->set('Content-Type', 'application/json');
    return $responseBack;
  }

  public function subscription_listAction() {
    /* @var $api InstagramAdapter */
    /* @var $subscriptions \Instaphp\Instagram\Subscriptions  */
    /* @var $response \Instaphp\Instagram\Response  */
    $api = $this->get('instagram');
    $subscriptions = $api->Subscriptions;
    try {
      $response = $subscriptions->ListSubscriptions();
    } catch (Exception $ex) {
      $this->get('logger')->info('CURL ERROR');
    }
    return $this->render('InstagramBundle:Subscription:list.html.twig', array('data' => $response->data));
  }

  public function subscription_deleteAction(Request $request, $type, $query) {
    /* @var $api InstagramAdapter */
    /* @var $subscriptions \Instaphp\Instagram\Subscriptions  */
    /* @var $response \Instaphp\Instagram\Response  */
    $api = $this->get('instagram');
    $subscriptions = $api->Subscriptions;
    $params = array('object' => $type);
    if ($query != 0) {
      $params = array('id' => $query);
    }

    $response = $subscriptions->Destroy($params);
    return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
  }

  public function subscriptionsAction($type, $query) {
    /* @var $api InstagramAdapter */
    /* @var $subscriptions \Instaphp\Instagram\Subscriptions  */
    $api = $this->get('instagram');
    $redirectTo = $this->generateUrl('instagram_subscription_update', array(), true);
    $subscriptions = $api->Subscriptions;
    $token = $this->container->getParameter('instagram.subscription.token');
    $subscriptions->Create($type, $redirectTo, $token, array('object_id' => $query));
    $response = new Response('thx api <3', Response::HTTP_OK);
    return $response;
  }

  public function logoutAction() {
    $isLoggedIn = $this->get('instaphp_token_handler')->logout();
    return $this->redirect($this->generateUrl($this->container->getParameter('instaphp.redirect_route_logout')));
  }

  public function thanksAction() {
    return $this->render('InstagramBundle:Default:thankyou.html.twig', array());
  }

  public function checkAction() {
    $isLoggedIn = $this->get('instaphp_token_handler')->isLoggedIn();
    return $this->redirect($this->generateUrl('instagram_homepage'));
  }

  private function storeInstagramResponse($tagsRecent, $em, $serializer, $instagramRepository, $type = null, $query = null) {
    $store = false;
    foreach ($tagsRecent->data as $tag) {
      $ii = $instagramRepository->find($tag['id']);
      $tagSerialized = $serializer->serialize($tag, 'json');
      $iin = $serializer->deserialize($tagSerialized, "Main\EntityBundle\Entity\InstagramImage", 'json');
      if (is_null($ii)) {
        $ii = $iin;
        $store = true;
        $em->persist($ii);
      } else {
//        $ii->updateAll($iin); // IF UPDATE INFO
      }
    }
    if ($store) {
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
        $this->get('logger')->info("VIOLATION");
      }
    }
    if (!is_null($type) && !is_null($query)) {
      $entryData = array(
          'category' => $type . '_' . $query
          , 'title' => $tagsRecent
          , 'when' => time()
      );
      // This is our new stuff 
      $context = new ZMQContext();
      $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
      $socket->connect("tcp://localhost:5555");
      $socket->send(json_encode($entryData));
    }
  }

}
