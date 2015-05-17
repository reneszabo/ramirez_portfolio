<?php

//CLAVE DE LA LLAVE DE GOOGLE notasecret

namespace Main\Page\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
    $logger = $this->get('logger');
    $content = $request->getContent();
    $params = json_decode($content); // 2nd param to get as array
    $data = new \StdClass();
    $logger->info("-----------------------------------------------------------");
    $logger->info("MESSAGE -> " . $params->head_commit->message);
    $logger->info("PUSHER NAME -> " . $params->pusher->name);
    $logger->info("PUSHER EMAIL -> " . $params->pusher->email);
    $logger->info("REPOSITORY FULL NAME -> " . $params->repository->full_name);
    $logger->info("REPOSITORY ULR -> " . $params->repository->html_url);
    $logger->info("REPOSITORY OWNER EMAIL -> " . $params->repository->owner->email);
    $response = new Response('thx github <3', Response::HTTP_OK);
    $message = \Swift_Message::newInstance()
            ->setSubject('GITHUB Push - ' . $params->pusher->name)
            ->setFrom('rene.ramirez@fersz.com')
            ->setTo($params->pusher->email)
            ->setContentType("text/html")
            ->setBody(
            $this->renderView(
                    'MainPageFrontendBundle:Email:email.html.twig', array('data' => $params)
            )
            )
    ;
    $this->get('mailer')->send($message);

    $LOCAL_ROOT = "/www";
    switch ($params->repository->full_name) {
      case "reneszabo/INGRAMCaribbeanSummit" :
        // Init vars
        $LOCAL_REPO_NAME = "ingram";
        break;
    }
    $LOCAL_REPO = "{$LOCAL_ROOT}/{$LOCAL_REPO_NAME}";
    shell_exec("cd {$LOCAL_REPO} && git git reset --hard &&  git pull");

    return $response;
  }

}
