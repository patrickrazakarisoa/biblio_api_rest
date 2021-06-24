<?php

namespace App\Services;

use App\Entity\Pret;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PretSubscriber implements EventSubscriberInterface
{
     public function __construct(TokenStorageInterface $token)
     {
          $this->token=$token;          
     }

     public static function getSubscribedEvents()
     {
          return[ KernelEvents::VIEW =>['getAuthenticateUser', EventPriorities::PRE_WRITE]  ];
     }

     public function getAuthenticateUser(ViewEvent $event)
     {
          $entity = $event->getControllerResult(); // récupère l'entité qui a déclanché l'évenement
          $method = $event->getRequest()->getMethod(); // récuprère la méthode invoquée dans la request
          $adherent = $this->token->getToken()->getUser(); // récupère l'adhérent actuallement connecté qui à lancé la request
          if ($entity instanceof Pret && $method == Request::METHOD_POST) { // si il s'agit d'une méthode POST sur l'entity Pret
               $entity->setAdherent($adherent); // on écrit l'adhérent dans la propriété adherant de l'entity Pret
          }
          return;
     }
}