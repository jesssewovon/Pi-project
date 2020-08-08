<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Request;

class RessettingController extends AbstractController
{
    /**
     * @Route("/ressetting", name="ressetting")
     */
    public function index()
    {
        return $this->render('ressetting/index.html.twig', [
            'controller_name' => 'RessettingController',
        ]);
    }

    /**
     * @Route("/ressetting_send_email", name="ressetting_send_email")
     */
    public function ressetting_send_email(Request $request, \Swift_Mailer $mailer, UserRepository $userRepository)
    {
    	$email_or_username = $_POST['reinit_mail'];
    	$user = $userRepository->findOneByEmail($email_or_username);
    	if (is_null($user)) {
    		$user = $userRepository->findOneByUsername($email_or_username);
    	}
    	if (is_null($user)) {
    		$response = new JsonResponse(["status" => "error", "message" => "Utilisateur n'existe pas"]);
            return $response;
    	}
    	$token = md5(uniqid());
    	$url = $request->getScheme().'://'.$request->getHttpHost();
    	$url1 = $this->generateUrl('ressetting', array('token' => $token));
    	$user->setTokenResseting($token);
    	$message = (new \Swift_Message('Hello Email'))
            // ->setFrom('jesssewovon@gmail.com')
            // ->setFrom('sewovonjess@gmail.com')
            ->setFrom('holdingMarkom@gmail.com')
            // ->setTo('sewovonjess@gmail.com')
            ->setTo('jesssewovon@gmail.com')
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/email.html.twig',
                    ['token' => $token, 'url' => $url.$url1]
                ),
                'text/html'
            )
        ;
        $mailer->send($message);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $response = new JsonResponse(["status" => "success", "message" => "Succès"]);
        return $response;
    }

    /**
     * @Route("/ressetting/{token}", name="ressetting", methods="GET")
     */
    public function ressetting($token, UserRepository $userRepository)
    {
    	$user = $userRepository->findOneByTokenResseting($token);
    	if (is_null($user)) {
    		return $this->render('ressetting/ressetting.html.twig', [
	            'message' => "xxxxxxx",
	        ]);
    	}
    	$defaultData = ['message' => 'Type your message here'];
        $form_resset = $this->createFormBuilder($defaultData)
            ->add('oldPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('repeatPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Rechercher', SubmitType::class)
        ;

        return $this->render('ressetting/ressetting.html.twig', [
            'form_resset' => $form_resset,
            'controller_name' => 'DefaultController',
        ]);
    }
}
