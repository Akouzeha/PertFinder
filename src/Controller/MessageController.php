<?php

namespace App\Controller;

use App\Entity\Message;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    /**
     * @Route("/message/sendmessage", name="send_message")
     */

    #[Route('/message/sendmessage', name: 'send_message')]
    public function sendMessage(EntityManagerInterface $em ,HubInterface $hub, Message $message): Response
    {
        $message->setUser($this->getUser());
        $message->setSentAt(new \DateTime());
        $em->persist($message);
        $em->flush();

        $update = new Update(
            'chat',
            json_encode(['message' => $message->getContenu(), 'user' => $message->getUser()->getUsername()])
        );

        $hub->publish($update);

        return new Response('Message sent!', Response::HTTP_OK);
    }
    
}
