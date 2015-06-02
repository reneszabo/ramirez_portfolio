<?php

namespace SocketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ZMQ;
use ZMQContext;

class DefaultController extends Controller {

  public function indexAction() {
    $entryData = array(
        'category' => 'kittensCategory'
        , 'title' => 'HOLA'
        , 'article' => 'EL ARTICULO'
        , 'when' => time()
    );
    // This is our new stuff 
    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect("tcp://localhost:5555");

    $socket->send(json_encode($entryData));


    return $this->render('SocketBundle:Default:index.html.twig', array());
  }

}
