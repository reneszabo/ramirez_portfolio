<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Page\FrontendBundle\EventListener;

use Main\Page\FrontendBundle\Event\GitMailEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Main\Page\FrontendBundle\Object\GitProfile;

/**
 * Description of GitMailEventListener
 *
 * @author Rene Roberto Ramirez Szabo <rene.szabo@gmail.com>
 */
class GitMailListener {

  protected $mailer;
  protected $logger;
  protected $templating;
  protected $emails = array();
  protected $githubers;

  public function __construct(\Swift_Mailer $mailer, LoggerInterface $logger, TwigEngine $templating) {
    $this->mailer = $mailer;
    $this->logger = $logger;
    $this->templating = $templating;
  }

  public function onGitMailEvent(GitMailEvent $event) {
    /* @var $request Request */
    $request = $event->getRequest();
    $logger = $this->logger;
    $templeting = $this->templating;
    if ($request instanceof Request && $request->isMethod(Request::METHOD_POST) && $request->headers->get('Content-Type') === 'application/json') {
      if ($request->headers->has('X-GitHub-Event')) {
        //GIT HUB
        $message = \Swift_Message::newInstance();
        $content = $request->getContent();
        $params = json_decode($content);
        /* GET CONTRIBUTORS */
        $browser = new \Buzz\Browser();
        $headers = array('User-Agent' => $params->repository->name);
        $response = $browser->get($params->repository->contributors_url, $headers);
        $contributors = json_decode($response->getContent());
        $this->addGitHuber(new GitProfile($params->pusher->login, $params->pusher->email));
        $this->addGitHuber(new GitProfile($params->repository->owner->name, $params->repository->owner->email));
        foreach ($contributors as $contributor) {
          $response = $browser->get($contributor->url, $headers);
          $user = json_decode($response->getContent());
          $pusher = $params->pusher->login == $user->login;
          $gp = new GitProfile($user->login, $user->email, $user->avatar_url, $user->name, $user->html_url, $pusher);
//          $response = $browser->get($user->avatar_url);
//          $logger->info($response);
//          $logger->info('--------------------------------');
//          $img2 = base64_encode($response->getContent());
//          $logger->info($img);
          $img = $message->embed(\Swift_Image::newInstance($user->avatar_url, 'image.png', 'image/png'));
          $logger->info('-----------------------');
          $logger->info($img);
          $logger->info('-----------------------');
          $gp->setImg($img);
          $this->addGitHuber($gp);
        }
        foreach ($this->githubers as $c) {
          $logger->info($c);
          if ($c->hasEmail()) {
            $this->emails[$c->getEmail()] = $c->getName();
          }
        }

        $message
                ->setSubject('GITHUB Push - ' . $params->pusher->name)
                ->setFrom('rene.ramirez@fersz.com')
                ->setTo($this->emails)
                ->setContentType("text/html")
                ->setBody(
                        $templeting->render(
                                'MainPageFrontendBundle:Email:email.html.twig', array('data' => $params, 'githubers' => $this->githubers)
                        )
                )
        ;
        $this->mailer->send($message);
//        $LOCAL_ROOT = "/srv/www";
//
//        switch ($params->repository->full_name) {
//          case "reneszabo/INGRAMCaribbeanSummit" :
//            // Init vars
//            $LOCAL_REPO_NAME = "ingram";
//            break;
//        }
//        $LOCAL_REPO = "{$LOCAL_ROOT}/{$LOCAL_REPO_NAME}";
//        $commando = "cd {$LOCAL_REPO} && sudo -u www-data git reset --hard && sudo -u www-data git pull";
//        $logger->info($commando);
//        shell_exec($commando);
      }
    }
  }

  private function addGitHuber(GitProfile $obj) {
    /* @var $gitprofile GitProfile */
    $new = true;
    foreach ($this->githubers as $gitprofile) {
      if ($gitprofile->getLogin() === $obj->getLogin()) {
        $new = false;
        if ($obj->getEmail() != '') {
          $gitprofile->setEmail($obj->getEmail());
        }
        $gitprofile->setAvatar_url($obj->getAvatar_url());
        $gitprofile->setName($obj->getName());
        $gitprofile->setHtml_url($obj->getHtml_url());
        break;
      }
    }

    if ($new) {
      $this->githubers[] = $obj;
    }
  }

}
