<?php

namespace Main\EntityBundle\Handler;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface {

  private $router;

  public function __construct(Router $router) {
    $this->router = $router;
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
    $referer = $request->headers->get('referer');
    if (empty($referer)) {
      return new RedirectResponse($this->router->generate('homepage'));
    } else {
      return new RedirectResponse($referer);
    }
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
    // Edit it to meet your requeriments
    $request->getSession()->set('login_error', $error);
    return new \Symfony\Component\HttpFoundation\RedirectResponse($this->router->generate('login_route'));
  }

}
