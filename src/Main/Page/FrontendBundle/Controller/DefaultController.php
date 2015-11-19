<?php

//CLAVE DE LA LLAVE DE GOOGLE notasecret

namespace Main\Page\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

  public function indexAction() {
    /* @var $a \Google_Service_Analytics_GaData */
    /* @var $storedAnalytics \Main\EntityBundle\Entity\Analytics */
    $client_email = $this->container->getParameter('google_email');
    $kernel = $this->get('kernel');
    $private_key = file_get_contents($kernel->locateResource('@MainPageFrontendBundle/GoogleKeys/humber.p12'));
    $scopes = array('https://www.googleapis.com/auth/analytics.readonly');
    $credentials = new \Google_Auth_AssertionCredentials(
            $client_email, $scopes, $private_key, 'notasecret'
    );
    $client = new \Google_Client();
    $client->setAssertionCredentials($credentials);
    if ($client->getAuth()->isAccessTokenExpired()) {
      $client->getAuth()->refreshTokenWithAssertion();
    }
    $analytics = new \Google_Service_Analytics($client);
    $optParams = array(
        'dimensions' => 'ga:city',
        'sort' => '-ga:sessions,ga:city',
        'max-results' => '6'
    );
    $a = null;
    $weHaveService = false;
    try {
      $a = $analytics->data_ga->get('ga:94452458', '2014-11-27', date('Y-m-d'), "ga:sessions", $optParams);
      $weHaveService = true;
    } catch (\Google_Service_Exception $e) {
      $weHaveService = false;
      $googleMessage = $e->getMessage();
    }
    $storedAnalytics = $this->getDoctrine()->getRepository('MainEntityBundle:Analytics')->find(1);
    $em = $this->getDoctrine()->getManager();
    if (is_null($storedAnalytics)) {
      $storedAnalytics = new \Main\EntityBundle\Entity\Analytics();
    }
    if ($weHaveService == true) {
      if ($storedAnalytics->getSessions() != $a->getTotalsForAllResults()['ga:sessions']) {
        
      }
    }
    $storedAnalytics->setSessions($a->getTotalsForAllResults()['ga:sessions']);
    $em->persist($storedAnalytics);
    $em->flush();

    return $this->render('MainPageFrontendBundle:Default:index.html.twig', array('cityVisits' => $a->getRows(), 'storedAnalytics' => $storedAnalytics));
  }

  /**
   * @Method({"POST"})
   */
  public function gameSaveAction(Request $request) {
    /* @var $game \Main\EntityBundle\Entity\Game */
    /* @var $gameObj \Main\EntityBundle\Entity\Game */
    if (!is_null($this->getUser())) {
      $gameRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Game');
      $game = $gameRepository->findOneBy(array('user' => $this->getUser()));
      $scoreBefore = -1;
      if (!is_null($game)) {
        $scoreBefore = $game->getScore();
      }
      $form = $this->createGameForm($game);
      $form->handleRequest($request);
      if ($form->isValid()) {
        $gameObj = $form->getData();
        $em = $this->getDoctrine()->getManager();
        if (!is_nan(($gameObj->getScore() * 1))) {
          if (is_null($game)) {
            $em->persist($gameObj);
            $em->flush();
          } else {
            if (($gameObj->getScore()) > ($scoreBefore)) {
              $em->persist($gameObj);
              $em->flush();
            }
          }
        }
      }
    }

    $gameRepository->clear();
    $players = $gameRepository->findBy(array(), array('score' => 'DESC'), 100);
    return $this->render('MainPageFrontendBundle:Game:players.html.twig', array('players' => $players, 'form' => $form->createView()));
  }

  private function createGameForm($game = null) {
    $user = $this->getUser();
    if (is_null($game)) {
      $game = new \Main\EntityBundle\Entity\Game();
    }
    $game->setUser($user);
    $url = $this->generateUrl('game_secure_save');
    $form = $this->createForm(
            new \Main\Page\FrontendBundle\Form\Type\GameType(), $game, array(
        'action' => $url,
        'method' => 'POST',
        'attr' => array(
            'rol' => 'form',
            'id' => 'scoreForm',
        ),
            )
    );
    return $form;
  }

  public function gameAction() {
    $gameRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Game');
    $players = $gameRepository->findBy(array(), array('score' => 'DESC'), 100);
    if (!is_null($this->getUser())) {
      $gameRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Game');
      $game = $gameRepository->findOneBy(array('user' => $this->getUser()));
      $form = $this->createGameForm($game);
      return $this->render('MainPageFrontendBundle:Game:index.html.twig', array('players' => $players, 'form' => $form->createView()));
    }
    return $this->render('MainPageFrontendBundle:Game:index.html.twig', array('players' => $players));
  }

  public function toDoListAction() {
    return $this->render('MainPageFrontendBundle:Humber/TodoList:index.html.twig', array());
  }

  public function fitritionAction() {
    return $this->render('MainPageFrontendBundle:Humber/Fitrition:index.html.twig', array());
  }

  public function symbiosisAction() {
    return $this->render('MainPageFrontendBundle:Humber/Symbiosis:index.html.twig', array());
  }

  public function oauth2callbackAction() {
    $logger = $this->get('logger');
    $logger->info('GOOGLE CALL BACK');
    $response = new Response('Ramirez Portfolio ', Response::HTTP_OK);
    return $response;
  }

  public function webhooksAction() {
    /* @var $request \Symfony\Component\HttpFoundation\Request */
    /* @var $logger \Symfony\Bridge\Monolog\Logger */
    $request = $this->get('request');
    $dispatcher = $this->container->get('event_dispatcher');
    $dispatcher->dispatch('git.mail.event', new \Main\Page\FrontendBundle\Event\GitMailEvent($request));

//    $browser = new \Buzz\Browser();
//    $response = $browser->get('https://avatars.githubusercontent.com/u/434504?v=3');
//    $img = base64_encode($response->getContent());
//    return $this->render('MainPageFrontendBundle::test.html.twig', array('img' => $img));
    $response = new Response('thx github <3', Response::HTTP_OK);
    return $response;
  }

  public function apiCallbackAction() {
    /* @var $request \Symfony\Component\HttpFoundation\Request */
    $request = $this->get('request');
    $response = new Response('thx api <3', Response::HTTP_OK);
    return $response;
  }

}
