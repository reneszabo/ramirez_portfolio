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

  public function indexAction() {
    /* @var $user \Main\EntityBundle\Entity\User */
    $user = $this->getUser();
    $userResponse = $user;
    return $this->render('InstagramBundle:Default:index.html.twig', array('user' => $user, 'userResponse' => $userResponse));
  }

  public function tagAction(Request $request, $query) {
    /* @var $serializer \JMS\Serializer\Serializer */
    /* @var $em \Doctrine\ORM\EntityManager */
    /* @var $instagramRepository \Main\EntityBundle\Entity\InstagramImageRepository */
    /* @var $user \Main\EntityBundle\Entity\User */
    /* @var $api InstagramAdapter */
    $user = $this->getUser();
    $api = $this->get('instagram');
    $response = $api->getTagRecent($query, $user->getInstagramAuthCode(), 10);
//    var_dump($response->data);
    $em = $this->getDoctrine()->getManager();
    $instagramRepository = $em->getRepository('Main\EntityBundle\Entity\InstagramImage');
    $this->storeInstagramResponse($response, $em, $this->get('jms_serializer'), $instagramRepository, 'tag', $query);
    $tags = $instagramRepository->findByTag($query);

    $form = $this->getFormRankDate($query);
    $usersRace = $this->managePostTagDate($request, $query, $form);
    return $this->render('InstagramBundle:Tag:index.html.twig', array('tagName' => $query, 'tags' => $tags, 'user' => $user, 'form' => $form->createView(), 'usersRace' => $usersRace, 'instagramInfo' => $response));
  }

  public function tagImagesAction(Request $request, $query, $page) {
    $result = array();
    /* @var $instagramRepository \Main\EntityBundle\Entity\InstagramImageRepository */
    $form = $this->getFormRankDate($query);
    $countImages = $this->managePostTagDateCountImage($request, $query, $form);
    $form = $this->getFormRankDate($query);
    $feed = $this->managePostImagesFeed($request, $query, $form, $page);
    $result['data'] = $feed;
    $result['total'] = $countImages * 1;
    $result['pages'] = ceil($countImages / 20) * 1; // number results / limit results in datababase
    $result['page'] = $page;
    if ($page >= $result['pages']) {
      $result['next_page'] = null;
    } else {
      $result['next_page'] = $page * 1 + 1;
    }
    $serializer = $this->get('jms_serializer');
    $tagSerialized = $serializer->serialize($result, 'json');
    return new \Symfony\Component\HttpFoundation\JsonResponse($tagSerialized, 200);
  }

  private function managePostImagesFeed($request, $query, \Symfony\Component\Form\Form $form, $page) {
    /* @var $fileds \InstagramBundle\Form\Object\InstagramDateFilter */
    /* @var $instagramRepository \Main\EntityBundle\Entity\InstagramImageRepository */
    $form->handleRequest($request);
    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $fileds = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $instagramRepository = $em->getRepository('Main\EntityBundle\Entity\InstagramImage');
        $images = $instagramRepository->findImagesWithDateRange($query, $fileds->getCreatedTimeStart(), $fileds->getCreatedTimeEnd(), $page);
        return $images;
      } else {
        return array();
      }
    } else {
      return array();
    }
  }

  private function managePostTagDateCountImage($request, $query, \Symfony\Component\Form\Form $form) {
    /* @var $fileds \InstagramBundle\Form\Object\InstagramDateFilter */
    /* @var $instagramRepository \Main\EntityBundle\Entity\InstagramImageRepository */
    $form->handleRequest($request);
    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $fileds = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $instagramRepository = $em->getRepository('Main\EntityBundle\Entity\InstagramImage');
        $imagesCount = $instagramRepository->countImagesInDateRange($query, $fileds->getCreatedTimeStart(), $fileds->getCreatedTimeEnd());
        return $imagesCount;
      } else {
        return 0;
      }
    } else {
      return 0;
    }
  }

  public function tagRankDateAction(Request $request, $query) {
    $form = $this->getFormRankDate($query);
    $usersRace = $this->managePostTagDate($request, $query, $form);
    $renderCards = $this->getUsersRankRender($usersRace);
    if ($request->isXmlHttpRequest()) {
      return new Response($renderCards);
    } else {
      return $this->redirect($request->headers->get('referer')); //{query}
    }
  }

  private function getFormRankDate($query) {
    $instagramDateFilter = new \InstagramBundle\Form\Object\InstagramDateFilter();
    $form = $this->createForm(
            new \InstagramBundle\Form\Type\InstagramDateFormFilter(), $instagramDateFilter, array(
        'action' => $this->generateUrl('instagram_tag_filter', array('query' => $query)),
        'method' => 'POST',
        'attr' => array(
            'rol' => 'form',
            'enctype' => 'multipart/form-data',
        ),
            )
    );
    return $form;
  }

  private function managePostTagDate($request, $query, \Symfony\Component\Form\Form $form) {
    /* @var $fileds \InstagramBundle\Form\Object\InstagramDateFilter */
    /* @var $instagramRepository \Main\EntityBundle\Entity\InstagramImageRepository */
    $form->handleRequest($request);
    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $fileds = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $instagramRepository = $em->getRepository('Main\EntityBundle\Entity\InstagramImage');
        $usersRace = $instagramRepository->findTop10UsersDateRange($query, $fileds->getCreatedTimeStart(), $fileds->getCreatedTimeEnd());
        return $usersRace;
      } else {
        return array();
      }
    } else {
      return array();
    }
  }

  private function getUsersRankRender($usersRace) {
    return $this->renderView('InstagramBundle:Tag:rank_list_carts.html.twig', array('usersRace' => $usersRace));
  }

  public function subscription_updateAction(Request $request) {
    /* @var $api InstagramAdapter */
    /* @var $response \InstagramBundle\Adapter\InstagramResponse  */
    /* @var $instagramSubscription \Main\EntityBundle\Entity\InstagramSubscription */
    $type = '';
    $query = '';
    $logger = $this->get('logger');
    if ('POST' === $request->getMethod()) {
      $dataString = $request->getContent();
      $data = json_decode($dataString, true);
      $em = $this->getDoctrine()->getManager();
      $instagramSubscriptionRepository = $em->getRepository('Main\EntityBundle\Entity\InstagramSubscription');
      $instagramRepository = $em->getRepository('Main\EntityBundle\Entity\InstagramImage');
      $api = $this->get('instagram');
      foreach ($data as $index => $value) {
        $instagramSubscription = $instagramSubscriptionRepository->findOneBy(array('instagramId' => $value["subscription_id"], 'isActive' => false));
        if ($instagramSubscription) {
          $instagramSubscription->setIsActive(true);
          $em->persist($instagramSubscription);
          $em->flush();
          $token = $instagramSubscription->getUser()->getInstagramAuthCode();
          $type = $instagramSubscription->getObject();
          $query = $instagramSubscription->getObjectId();
          $lastMinId = $instagramSubscription->getLastMinId();
          if (is_null($lastMinId)) {
            $lastMinId = "yeap";
          }
          $response = null;
          while ($lastMinId != null) {
            if ($lastMinId === "yeap") {
              $response = $api->getTagRecent($query, $token, 100);
            } else {
              $response = $api->getTagRecent($query, $token, 100, $lastMinId);
            }
            if (array_key_exists("min_tag_id", $response->pagination)) {
              $lastMinId = $response->pagination['min_tag_id'];
              $instagramSubscription->setLastMinId($lastMinId);
            } else {
              $lastMinId = null;
            }
            if (!is_null($response)) {
              $this->storeInstagramResponse($response, $em, $this->get('jms_serializer'), $instagramRepository, 'tag', $query);
            }
            $logger->info($lastMinId);
          }
          $instagramSubscription->setLastMinId($lastMinId);
          $instagramSubscription->setIsActive(false);
          $em->persist($instagramSubscription);
          $em->flush();
        } else {
          $logger->info("NOP");
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
    $api = $this->get('instagram');
    $response = $api->getSubscription();
    return $this->render('InstagramBundle:Subscription:list.html.twig', array('data' => $response->data));
  }

  public function subscription_deleteAction(Request $request, $type, $query) {
    /* @var $api InstagramAdapter */
    $api = $this->get('instagram');
    $params = array('object' => $type);
    if ($query != 0) {
      $params = array('id' => $query);
    }
    $response = $api->deleteSubscription($params);
    return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
  }

  public function subscriptionsAction($type, $query) {
    /* @var $api InstagramAdapter */
    /* @var $response \InstagramBundle\Adapter\InstagramResponse */
    $api = $this->get('instagram');
    $em = $this->getDoctrine()->getManager();
    $instagramSubscriptionRepository = $em->getRepository('Main\EntityBundle\Entity\InstagramSubscription');
    $redirectTo = $this->get('router')->generate('instagram_subscription_update', array(), true);
    $verifyToken = $this->container->getParameter('instagram.subscription.token');
    $response = $api->postSubscription($type, $redirectTo, $verifyToken, array('object_id' => $query));
    $this->get('logger')->info(json_encode($response->data));
    if ($response->meta['code'] == 200) {
      $instagramSubscription = $instagramSubscriptionRepository->findOneBy(array('instagramId' => $response->data['id']));
      if (!$instagramSubscription) {
        $this->get('logger')->info(json_encode("CRE"));
        $instagramSubscription = new \Main\EntityBundle\Entity\InstagramSubscription();
        $instagramSubscription->setAspect($response->data['aspect']);
        $instagramSubscription->setCallbackUrl($response->data['callback_url']);
        $instagramSubscription->setInstagramId($response->data['id']);
        $instagramSubscription->setObject($response->data['object']);
        $instagramSubscription->setObjectId($response->data['object_id']);
        $instagramSubscription->setType($response->data['type']);
        $instagramSubscription->setUser($this->getUser());
        $instagramSubscription->setIsActive(false);
        $em->persist($instagramSubscription);
        $em->flush();
      }

//    $instagramSubscription = new \Main\EntityBundle\Entity\InstagramSubscription();
//    $instagramSubscription->setUser($this->getUser());
    }
    $response = new Response('thx api <3', Response::HTTP_OK);
    return $response;
  }

  public function checkAction() {
    $isLoggedIn = $this->get('instaphp_token_handler')->isLoggedIn();
    return $this->redirect($this->generateUrl('instagram_homepage'));
  }

  private function storeInstagramResponse($tagsRecent, $em, $serializer, $instagramRepository, $type = null, $query = null) {
    $store = false;
    foreach ($tagsRecent->data as $tag) {
      if ($tag['type'] != 'video') {
        $ii = $instagramRepository->find($tag['id']);
        $tagSerialized = $serializer->serialize($tag, 'json');
        $iin = $serializer->deserialize($tagSerialized, "Main\EntityBundle\Entity\InstagramImage", 'json');
        if (is_null($ii)) { 
          $fb = new \Firebase\FirebaseLib('https://torrid-torch-1189.firebaseio.com/','I9kTlESj2EbtrUTaNrypX96iG54r6ozmKlJlehIR');
          $ii = $iin;
          $store = true;
          $em->persist($ii);
          $fb->set('images/'.$query.'/'.$ii->getId(),$tag);
        } else {
//        $ii->updateAll($iin); // IF UPDATE INFO
        }
      }
    }
    if ($store) {
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
        $this->get('logger')->info("VIOLATION");
      }
    }
    $propagate = $type . '_' . $query;
    if (!is_null($type) && !is_null($query)) {
      $this->get('logger')->info("BOOOOM -> " . $propagate);
      $this->get('logger')->info(count($tagsRecent));
      $entryData = array(
          'category' => $propagate
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

}
