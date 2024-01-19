<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ChannelRepository;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'send_message')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);
        $message = new Message();


        
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData(); // $message is an instance of Message
            $user = $this->getUser();
            $message->setUser($user);
            $message->setSentAt(new \DateTime());
            
            $em->persist($message);
            $em->flush();
            $this->addFlash('success', 'Message a été envoyé!');
            return $this->redirectToRoute('send_message');
        
        }
        return $this->render('message/index.html.twig', [
            'form' => $form,
        ]);
    
    }
}
