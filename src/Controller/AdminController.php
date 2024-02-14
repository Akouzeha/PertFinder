<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\ResponseType;
use App\Form\UserPromoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();
        $requestCount = $em->getRepository(User::class)->countDeleteRequests();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'requestCount' => $requestCount,
            'users' => $users,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'admin_edit_user')]
    public function showUser(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $userId = $user->getId();

        // Check if the user has the "super admin" role
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            $this->addFlash('warning', 'You cannot modify the roles of a super admin.');
            return $this->redirectToRoute('app_admin');
        }

        // Change the role of the user
        $form = $this->createForm(UserPromoteType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Les rôles de l\'utilisateur ont été modifiés avec succès.');
            return $this->redirectToRoute('admin_edit_user', ['id' => $userId]);
        }

        return $this->render('admin/edit.html.twig', [
            'controller_name' => 'AdminController',
            'user' => $user,
            'userId' => $userId,
            'form' => $form,
        ]);
    }
    /**
     * @Route("/admin/{id}/delete", name="admin_delete_user")
     * @param User $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/admin/{id}/delete', name: 'admin_delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        // Check if the user has the "super admin" role
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            $this->addFlash('warning', 'You cannot pseudonymize a super admin.');
            return $this->redirectToRoute('app_admin');
        }
        else if($this->isGranted('ROLE_ADMIN')){
        // Pseudonymize or anonymize personal data
        $hashedUsername = hash('sha256', $user->getUsername());
        $hashedEmail = hash('sha256', $user->getEmail());
        // Disable the user's account
        $user->setIsDeleted(true);
        
        $email = (new TemplatedEmail())
        ->from('noReplay@admin.com')
        ->to($user->getEmail())
        ->subject('Confirmation de suppression de compte')
        ->htmlTemplate('message/account_delete_confirmation.html.twig')
        ->context([
            'user' => $user,
        ]);
        $mailer->send($email);
        
        // Set the pseudonymized data
        $user->setUsername($hashedUsername);
        $user->setEmail($hashedEmail);
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'L\'utilisateur a été pseudonymisé avec succès.');
    }

        return $this->redirectToRoute('app_admin');
    }
    #[Route('/admin/messages', name: 'recevoir_messages')]
    public function recevoirMessages(EntityManagerInterface $em): Response
    {
        //find parent messages only
        $messages = $em->getRepository(Message::class)->findBy(['parentMessage' => null]);
        return $this->render('admin/message.html.twig', [
            
            'messages' => $messages,
        ]);
    }
    #[Route('/admin/message-lock/{id}', name: 'lock_message')]
    public function lockMessage(EntityManagerInterface $em, $id): Response
    {
        $message = $em->getRepository(Message::class)->find($id);
        if (!$message) {
            throw $this->createNotFoundException('Message not found');
        }
        $message->setAnsewred(true);
        $em->persist($message);
        $em->flush();
        $this->addFlash('success', 'Message a été traité!');
        return $this->redirectToRoute('respond_message', ['messageId' => $id]);
    }
   

}
