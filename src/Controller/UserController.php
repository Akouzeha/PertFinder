<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UploadImageType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function showUser(User $user, Request $request, $id, EntityManagerInterface $em): Response
    {   
        $user = $em->getRepository(User::class)->find($id);
        //check if the user is changing his own aboutMe
        if($this->getUser() != $user) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier ce profil!');
            return $this->redirectToRoute('info_user', ['id' => $this->getUser()->getId()]);
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Update the aboutMe
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Présentation est modifié avec sucées!');
            return $this->redirectToRoute('info_user', ['id' => $id]);
        }

        return $this->render('user/show.html.twig', [
            'aboutMeForm' => $form,
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
    public function editUserName(User $user,$id, EntityManagerInterface $em, Request $request): Response
    {
        //all user info
        $user = $em->getRepository(User::class)->find($id);
        //check if the user is changing his own username
        if($this->getUser() != $user) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier ce profil!');
            return $this->redirectToRoute('info_user', ['id' => $this->getUser()->getId()]);
        }
        $form = $this->createFormBuilder($user)

        ->add('username', TextType::class, [
            'attr' => [
            'class' => 'form-change'
            ],
            'constraints' => [
                new NotBlank(),
                new Regex([
                    'pattern' => "/^[a-zA-Z0-9]*$/",
                    'message' => 'Les caractères spéciaux ne sont pas autorisés! ',
                ]),
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
    public function editMail(User $user, $id, EntityManagerInterface $em, Request $request): Response
    {
        //all user info
        $user = $em->getRepository(User::class)->find($id);
        //check if the user is changing his own email
        if($this->getUser() != $user) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier ce profil!');
            return $this->redirectToRoute('info_user', ['id' => $this->getUser()->getId()]);
        }
        $form = $this->createFormBuilder($user)

        ->add('email', TextType::class, [
            'attr' => [
            'class' => 'form-change'
            ],
            'constraints' => [
                new NotBlank(),
                
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
    #[Route('/user/{id}/changephoto', name: 'change_photo')]
    public function changeProfilePic(Request $request,SluggerInterface $slugger ,EntityManagerInterface $em, User $user, $id)
    {
        //all user info
        $user = $em->getRepository(User::class)->find($id);
        //check if the user is changing his own photo
        if($this->getUser() != $user) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier ce profil!');
            return $this->redirectToRoute('info_user', ['id' => $this->getUser()->getId()]);
        }
        $form = $this->createForm(UploadImageType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile === null) {
                $this->addFlash('error', 'Veuillez choisir une photo!');
                return $this->redirectToRoute('show_user', ['id' => $id]);
            }
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
            $imgFilePath = 'img/profile_pics/'.$newFilename;
            // Move the file to the directory where brochures are stored
            try {
                $imageFile->move(
                    //profile_pics_directory is defined in services.yaml
                    $this->getParameter('profile_pics_directory'),
                    $newFilename
                );
                $user->setImgName($imgFilePath);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                $this->addFlash('error', 'Erreur lors de l\'upload de la photo!');
                return $this->redirectToRoute('show_user', ['id' => $id]);
            }

            $em->flush();
            $this->addFlash('success', 'Photo est modifié avec sucées!');
            return $this->redirectToRoute('show_user', ['id' => $id]);
        }
        return $this->render('user/info.html.twig', [
            'profilePicForm' => $form,
            'userInfo' => $user,
            'viewedId' => $id
        ]);
    }

    #[Route('/user/{id}/request-delete', name: 'delete_request')]
    public function requestDelete(User $user, $id, EntityManagerInterface $em): Response
    {
        //ask an admin to delete the account
        $user = $em->getRepository(User::class)->find($id);
        //check if the user is demanding to delete his own account
        if($this->getUser() != $user) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier ce profil!');
            return $this->redirectToRoute('info_user', ['id' => $this->getUser()->getId()]);
        }
        //check if the user is already asking to delete his account
        if($user->isRequestDelete()) {
            $this->addFlash('error', 'Vous avez déja demandé la suppression de votre compte!');
            return $this->redirectToRoute('info_user', ['id' => $id]);
        }
        $user->setRequestDelete(true);
        $em->flush();
        $this->addFlash('success', 'Votre demande est envoyée!');
        return $this->render('user/info.html.twig', [
            'userInfo' => $user,
            'viewedId' => $id
        ]);
        
    }
    
}
