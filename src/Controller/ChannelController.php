<?php

namespace App\Controller;

use App\Entity\Channel;
use App\Repository\ChannelRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChannelController extends AbstractController
{
    #[Route('/channel', name: 'app_channel')]
    public function index(ChannelRepository $cr): Response
    {
        $channels = $cr->findAll();
        return $this->render('channel/index.html.twig', [
            'channels' => $channels ?? [],
        ]);
    }

    #[Route('/chat/{id}', name: 'chat')]
    public function chat( Channel $channel, MessageRepository $messageRepository ): Response
    {
        $messages = $messageRepository->findBy([
            'channel' => $channel
        ], ['sentAt' => 'ASC']);

        return $this->render('channel/chat.html.twig', [
            'channel' => $channel,
            'messages' => $messages
        ]);
    }
}
