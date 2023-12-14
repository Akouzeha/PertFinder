<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/{id}', name: 'info_user')]
    public function showUser($id, EntityManagerInterface $em): Response
    {   
        $user = $em->getRepository(User::class)->find($id);
        return $this->render('user/show.html.twig', [
            'viewedId' => $id,
            'userInfo' => $user
        ]);
    }

    #[Route('/user/{id}/show', name: 'show_user')]
    public function editUser($id, EntityManagerInterface $em): Response
    {
        //all user info
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('user/info.html.twig', [
            'userInfo' => $user,
            'viewedId' => $id
        ]);
    }

    #[Route('/user/{id}/edit-usernme', name: 'edit_user_name')]
    public function editUserName($id, EntityManagerInterface $em, Request $request): Response
    {
        //all user info
        $user = $em->getRepository(User::class)->find($id);
        $form = $this->createFormBuilder($user)

        ->add('username', TextType::class, [
            'attr' => [
            'class' => 'form-change'
            ],
        ])
        ->add('save', SubmitType::class, ['label' => 'Changer Username',
        'attr' => ['class' => 'edit-btn']])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Update the username
            $newUsername = $form->get('username')->getData();
            //check if username exist
            $userExist = $em->getRepository(User::class)->findBy(['username' => $newUsername]);
            if ($userExist) {
                $this->addFlash('error', 'Username existe déja!');
                return $this->redirectToRoute('show_user', ['id' => $id]);
            }
            $user->setUsername($newUsername);
            $em->flush();
            $this->addFlash('success', 'Username est modifié avec sucées!');
            return $this->redirectToRoute('show_user', ['id' => $id]);
        }
        return $this->render('user/info.html.twig', [
            'userNameForm' => $form->createView(),
            'userInfo' => $user,
            'viewedId' => $id
        ]);
    }

    #[Route('/user/{id}/edit-email', name: 'edit_email')]
    public function editMail($id, EntityManagerInterface $em, Request $request): Response
    {
        //all user info
        $user = $em->getRepository(User::class)->find($id);
        $form = $this->createFormBuilder($user)

        ->add('email', TextType::class, [
            'attr' => [
            'class' => 'form-change'
            ],
        ])
        ->add('save', SubmitType::class, ['label' => 'Changer Email',
        'attr' => ['class' => 'edit-btn']])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Update the email
            $newEmail = $form->get('email')->getData();
            //check if email is the same
            $mailExist = $em->getRepository(User::class)->findBy(['email' => $newEmail]);
            if ($mailExist) {
                $this->addFlash('error', 'Email existe déja!');
                return $this->redirectToRoute('show_user', ['id' => $id]);
            }
            elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Email est invalide!');
                return $this->redirectToRoute('show_user', ['id' => $id]);
            }
            else {
            $user->setEmail($newEmail);
            $em->flush();
            $this->addFlash('success', 'Email est modifié avec sucées!');
            return $this->redirectToRoute('show_user', ['id' => $id]);
            }
        }
        return $this->render('user/info.html.twig', [
            'emailForm' => $form->createView(),
            'userInfo' => $user,
            'viewedId' => $id
        ]);
    }

    #[Route('/user/{id}/edit-prenom', name: 'edit_prenom')]
    public function editPrenom($id, EntityManagerInterface $em, Request $request): Response
    {
        //all user info
        $user = $em->getRepository(User::class)->find($id);
        $form = $this->createFormBuilder($user)

        ->add('prenom', TextType::class, [
            'attr' => [
            'class' => 'form-change'
            ],
        ])
        ->add('save', SubmitType::class, ['label' => 'Changer Prénom',
        'attr' => ['class' => 'edit-btn']])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Update the prenom
            $newPrenom = $form->get('prenom')->getData();
            //check if prenom is valid
            if (!preg_match("/^[a-zA-Z-' ]*$/",$newPrenom)) {
                $this->addFlash('error', 'Prénom est invalide!');
                return $this->redirectToRoute('show_user', ['id' => $id]);
            }
            
            $user->setPrenom($newPrenom);
            $em->flush();
            $this->addFlash('success', 'Prénom est modifié avec sucées!');
            return $this->redirectToRoute('show_user', ['id' => $id]);
        }
        return $this->render('user/info.html.twig', [
            'prenomForm' => $form->createView(),
            'userInfo' => $user,
            'viewedId' => $id
        ]);
    }

    #[Route('/user/{id}/edit-nom', name: 'edit_nom')]
    public function editNom($id, EntityManagerInterface $em, Request $request): Response
    {
        //all user info
        $user = $em->getRepository(User::class)->find($id);
        $form = $this->createFormBuilder($user)

        ->add('nom', TextType::class, [
            'attr' => [
            'class' => 'form-change'
            ],
        ])
        ->add('save', SubmitType::class, ['label' => 'Changer Nom',
        'attr' => ['class' => 'edit-btn']])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Update the nom
            $newNom = $form->get('nom')->getData();
            //check if nom is valid
            if (!preg_match("/^[a-zA-Z-' ]*$/",$newNom)) {
                $this->addFlash('error', 'Nom est invalide!');
                return $this->redirectToRoute('show_user', ['id' => $id]);
            }
            
            $user->setNom($newNom);
            $em->flush();
            $this->addFlash('success', 'Nom est modifié avec sucées!');
            return $this->redirectToRoute('show_user', ['id' => $id]);
        }
        return $this->render('user/info.html.twig', [
            'nomForm' => $form->createView(),
            'userInfo' => $user,
            'viewedId' => $id
        ]);
    }

    
}
