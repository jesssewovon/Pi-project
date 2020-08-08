<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Repository\UserRepository;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register__k", name="app_register__k")
     */
    public function register__k(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_VENDEUR']);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, UserRepository $userRepository): Response
    {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $telephone = $_POST['telephone'];
        $email = $_POST['register_mail'];
        $register_username = $_POST['register_username'];
        $password = $_POST['password'];
        $conf = $_POST['conf'];
        $adresse = $_POST['adresse'];

        $user = $userRepository->findOneByEmail($email);
        if (!is_null($user)) {
            return new JsonResponse(["status" => "error", "message" => "Email déjà existant"]);
        }
        $user = $userRepository->findOneByUsername($register_username);
        if (!is_null($user)) {
            return new JsonResponse(["status" => "error", "message" => "Nom d'utilisateur déjà existant"]);
        }
        if ($password != $conf) {
            return new JsonResponse(['status' => 'error', 'message' => 'Mot de passe et confirmation non conformes']);
        }

        $user = new User();
        $user->setNom($nom);
        $user->setPrenoms($prenom);
        $user->setNumTel($telephone);
        $user->setEmail($email);
        $user->setUsername($register_username);
        $user->setAdresse($adresse);

        $user->setRoles(['ROLE_VENDEUR']);

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $authenticator,
            'main' // firewall name in security.yaml
        );
        
        return new JsonResponse(['status' => 'success']);
    }
}
