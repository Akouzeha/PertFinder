<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Form\ResponseType;
use App\Repository\ChannelRepository;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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
            $message->setAnsewred(false);
            $em->persist($message);
            $em->flush();
            $this->addFlash('success', 'Message a été envoyé!');
            return $this->redirectToRoute('send_message');
        
        }
        return $this->render('message/index.html.twig', [
            'form' => $form,
        ]);
    
    }
    #[Route('/message/respond/{messageId}', name: 'respond_message')]
    public function respond(Request $request, EntityManagerInterface $em, $messageId, MailerInterface $mailer): Response
    {
        
        $messageParent = $em->getRepository(Message::class)->find($messageId);
        $user = $messageParent->getUser();
        if (!$messageParent) {
            throw $this->createNotFoundException('Parent message not found');
        }
        if(!$this->isGranted('ROLE_ADMIN') and $this->getUser() != $messageParent->getUser()){
            $this->addFlash('warning', 'Vous n\'avez pas le droit de répondre à ce message');
            return $this->redirectToRoute('app_home');
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
            $message->setAnsewred(false);
            $em->persist($message);
            $em->flush();
            
            //send email to the user
            $email = (new TemplatedEmail())
            ->from('noreplay@pertfinder.com')
            ->to($user->getEmail())
            ->subject('Réponse à votre message')
            ->htmlTemplate('message/reponse_confirmation.html.twig')
            ->context([
            'user' => $user,
            'message' => $messageParent,
        ]);
        $mailer->send($email);

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
    #[Route('/message/newresponses', name: 'new_responses')]
    public function fetchNewresponses(EntityManagerInterface $em, Message $messageParent): Response
    {
        $messageParentId = $messageParent->getId();
        if (!$messageParent) {
            throw $this->createNotFoundException('Parent message not found');
        }
        //find all responses
        $responses = $em->getRepository(Message::class)->findBy(['parentMessage' => $messageParentId]);
        return $this->render('message/newresponses.html.twig', [
            'messageParentId' => $messageParentId,
            'responses' => $responses,
        ]);
    }
    
}
