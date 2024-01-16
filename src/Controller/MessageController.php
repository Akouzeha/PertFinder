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
    #[Route('/message', name: 'app_message')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response($this->render('message/message.stream.html.twig', [
                'message' => $form->getData(),
                'form' => $form->createView(),
            ]));
        
        }
        return $this->render('message/index.html.twig', [
            'form' => $form,
        ]);
    
    }

    /**
     * @Route("/message/send", name="send_message")
     */

    #[Route('/message/send', name: 'send_message')]
    public function sendMessage(Request $request, ChannelRepository $channelRepository,
    EntityManagerInterface $em): Response
    {
        
    }
}
