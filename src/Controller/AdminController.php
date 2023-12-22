<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPromoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
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
            $this->addFlash('success', 'Les rôles de l\'utilisateur ont été modifiés');
            return $this->redirectToRoute('admin_edit_user', ['id' => $userId]);
        }

        return $this->render('admin/edit.html.twig', [
            'controller_name' => 'AdminController',
            'user' => $user,
            'userId' => $userId,
            'form' => $form,
        ]);
    }


}
