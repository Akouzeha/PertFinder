<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Form\ResponseType;
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
            $message = $form->getData(); 
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
    #[Route('/message/respond/{id}/{messageId}', name: 'respond_message')]
    public function respond(Request $request, EntityManagerInterface $em, $messageId): Response
    {
        
        $messageParent = $em->getRepository(Message::class)->find($messageId);
        if (!$messageParent) {
            throw $this->createNotFoundException('Parent message not found');
        }
        $form = $this->createForm(ResponseType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = new Message();
            $message = $form->getData(); 
            $user = $this->getUser();
            $message->setUser($user);
            if($this->isGranted('ROLE_ADMIN')){
                $message->setEmail('admin@pertfinder.com');
            }
            else{
                $message->setEmail($user->getEmail());
            }
            $message->setSujet($messageParent->getSujet());
            $message->setSentAt(new \DateTime());
            $message->setParentMessage($messageParent);
            $message->setAnsewred(true);
            $em->persist($message);
            $em->flush();
            $this->addFlash('success', 'Message a été envoyé!');
            return $this->redirectToRoute('respond_message', ['id' => $messageId, 'messageId' => $messageId]);
            
        }
        //find all responses
        $responses = $em->getRepository(Message::class)->findBy(['parentMessage' => $messageId]);
        return $this->render('message/responses.html.twig', [
            'formResponse' => $form,
            'messageParent' => $messageParent,
            'responses' => $responses,
        ]);
    }

    
}
