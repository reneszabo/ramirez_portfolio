<?php

namespace SocketBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use React\EventLoop\Factory;
use SocketBundle\Adapter\Pusher;
use React\ZMQ\Context;
use ZMQ;
use React\Socket\Server;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;

/**
 * Description of RachetCommand
 *
 * @author Rene Roberto Ramirez Szabo <rene.szabo@gmail.com>
 */
class RachetCommand extends ContainerAwareCommand {

  protected function configure() {
    $this->setName('websocket:listen')
            ->setDescription('Listen for websocket requests (blocks indefinitely)');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    $loop = Factory::create();
    $pusher = new Pusher();

// Listen for the web server to make a ZeroMQ push after an ajax request
    $context = new Context($loop);
    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
    $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
    $pull->on('message', array($pusher, 'onBlogEntry'));

// Set up our WebSocket server for clients wanting real-time updates
    $webSock = new Server($loop);
    $webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
    $webServer = new IoServer(
            new HttpServer(
            new WsServer(
            new WampServer(
            $pusher
            )
            )
            ), $webSock
    );

    $loop->run();
  }

}
