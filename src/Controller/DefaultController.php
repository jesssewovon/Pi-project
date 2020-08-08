<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;

use App\Entity\Produit;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\User;
use App\Form\RegistrationFormType;

use Datetime;
use DateTimeZone;

class DefaultController extends AbstractController
{
    public function now_date_by_timezone()
    {
        /*$tz = new DateTimeZone('Europe/Paris');
        $date = new DateTime('2012-03-09 17:26:30', $tz);//utilisera le bon fuseau horaire*/

        $date = new DateTime();
        //$tz = new DateTimeZone('Europe/London');
        //$tz = new DateTimeZone('Europe/Paris');
        $tz = new DateTimeZone('UTC');
        $date->setTimezone($tz);//et l'heure est modifiée en fonction :-)
        return $date;
    }

    public function date_by_timezone($date, $timezone)
    {
        $tz = new DateTimeZone($timezone);
        $date->setTimezone($tz);//et l'heure est modifiée en fonction :-)
        return $date;
    }
    ///////////////////////////////////////////////////////////////////////
    /**
     * @Route("/default", name="default")
     */
    public function default(Request $request, CategorieRepository $categorieRepository, ProduitRepository $produitRepository)
    {
        $user = new User();
        $user->setRoles(['ROLE_VENDEUR']);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        ////////////////////////////////////////////////////////////
        $session = $request->getSession();
        $session->set('categorie', NULL);
        $session->set('mot', '');
        $session->set('dateDebut', NULL);
        $session->set('dateFin', NULL);

        $data = $produitRepository->finding(1, '', NULL, NULL, NULL);
        $all = $data['all'];
        $resultat = $data['resultat'];
        $tous_produits = $produitRepository->findAll();

        return $this->render('default/default.html.twig', [
            'controller_name' => 'DefaultController',
            'registrationForm' => $form->createView(),
            'all' => $all,
            'produits' => $resultat,
            'tous_produits' => $tous_produits,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, CategorieRepository $categorieRepository, ProduitRepository $produitRepository)
    {
        $user = new User();
        $user->setRoles(['ROLE_VENDEUR']);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        ////////////////////////////////////////////////////////////
        $session = $request->getSession();
        $session->set('categorie', NULL);
        $session->set('mot', '');
        $session->set('dateDebut', NULL);
        $session->set('dateFin', NULL);

        $data = $produitRepository->finding(1, '', NULL, NULL, NULL);
        $all = $data['all'];
        $resultat = $data['resultat'];
        $tous_produits = $produitRepository->findAll();

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'registrationForm' => $form->createView(),
            'all' => $all,
            'produits' => $resultat,
            'tous_produits' => $tous_produits,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/instruction", name="instruction")
     */
    public function instruction(ProduitRepository $produitRepository)
    {
        return $this->render('default/instruction.html.twig', [
            'produits' => $produitRepository->findBy([], [], 3),
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/publication{id}unique", name="publication_unique")
     */
    public function publication_unique(ProduitRepository $produitRepository, Produit $produit)
    {
        return $this->render('default/publication_unique.html.twig', [
            'produit' => $produit,
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        return $this->render('default/test.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        return $this->render('default/dashboard.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/interdit", name="interdit")
     */
    public function interdit()
    {
        return $this->render('default/interdit.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/all", name="all")
     */
    public function all(Request $request, CategorieRepository $categorieRepository, ProduitRepository $produitRepository)
    {
        $session = $request->getSession();
        $session->set('categorie', NULL);
        $session->set('mot', '');
        $session->set('dateDebut', NULL);
        $session->set('dateFin', NULL);

        $data = $produitRepository->finding(1, '', NULL, NULL, NULL);
        $all = $data['all'];
        $resultat = $data['resultat'];

        return $this->render('default/search.html.twig', [
            'controller_name' => 'DefaultController',
            'mot' => '',
            'all' => $all,
            'produits' => $resultat,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/word_search", name="word_search", methods="GET")
     */
    public function word_search(Request $request, CategorieRepository $categorieRepository, ProduitRepository $produitRepository)
    {
        $mot = $_GET['mot'];

        $session = $request->getSession();
        $session->set('categorie', NULL);
        $session->set('mot', $mot);
        $session->set('dateDebut', NULL);
        $session->set('dateFin', NULL);
        ///////////////////////////////////
        
        $data = $produitRepository->finding(1, $mot, NULL, NULL, NULL);
        $all = $data['all'];
        $resultat = $data['resultat'];
        // dump($produits);exit;
        return $this->render('default/search.html.twig', [
            'mot' => $mot,
            'all' => $all,
            'produits' => $resultat,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/categorie_search", name="categorie_search", methods="GET")
     */
    public function categorie_search(Request $request, CategorieRepository $categorieRepository, ProduitRepository $produitRepository, $id)
    {
        $categorie = $categorieRepository->findOneById($id);

        $session = $request->getSession();
        $session->set('categorie', $categorie);
        $session->set('mot', '');
        $session->set('dateDebut', NULL);
        $session->set('dateFin', NULL);
        // $mot = $_POST['mot'];
        $data = $produitRepository->finding(1, '', $categorie, NULL, NULL);
        $all = $data['all'];
        $resultat = $data['resultat'];
        // dump($produits);exit;
        return $this->render('default/search.html.twig', [
            // 'mot' => $mot,
            'all' => $all,
            'produits' => $resultat,
            'categorie' => $categorie,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{periode}/periode_search", name="periode_search", methods="GET")
     */
    public function periode_search(Request $request, CategorieRepository $categorieRepository, ProduitRepository $produitRepository, $periode)
    {
        if ($periode == 'jour') {
            $dateDebut = $this->now_date_by_timezone();
            $dateFin = $this->now_date_by_timezone();
            $periode_text = "du jour";
        }
        if ($periode == 'hier') {
            $dateDebut = new Datetime('yesterday');
            $dateFin = new Datetime('yesterday');
            $periode_text = "d'hier";
        }
        if ($periode == 'semaine') {
            $dateDebut = new Datetime('last monday');
            $dateFin = new Datetime('now');
            $periode_text = "de la semaine";
        }
        if ($periode == 'semainePassee') {
            $dateDebut = new Datetime('last sunday');
            $dateFin = new Datetime('last sunday');
            $periode_text = "de la semaine passée";

            $dateDebut->sub(new \DateInterval('P6D'));
        }
        if ($periode == 'mois') {
            $dateDebut = new Datetime('first day of this month');
            $dateFin = new Datetime('now');
            $periode_text = "du mois";
        }
        if ($periode == 'moisPasse') {
            $dateDebut = new Datetime('first day of previous month');
            $dateFin = new Datetime('last day of previous month');
            $periode_text = "du mois passé";
        }
        $dateDebut = $this->date_by_timezone($dateDebut, 'UTC');
        $dateFin = $this->date_by_timezone($dateFin, 'UTC');
        // dump($dateDebut);
        // dump($dateFin);exit;

        $session = $request->getSession();
        $session->set('categorie', NULL);
        $session->set('mot', '');
        $session->set('dateDebut', $dateDebut);
        $session->set('dateFin', $dateFin);
        // $mot = $_POST['mot'];
        $data = $produitRepository->finding(1, '', NULL, $dateDebut, $dateFin);
        $all = $data['all'];
        $resultat = $data['resultat'];
        // dump($produits);exit;
        return $this->render('default/search.html.twig', [
            // 'mot' => $mot,
            'periode_text' => $periode_text,
            'all' => $all,
            'produits' => $resultat,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/search", name="search_ajax", methods="POST")
     */
    public function search_ajax(Request $request, ProduitRepository $produitRepository)
    {
        $session = $request->getSession();

        // $session->set('categorie', NULL);
        // $session->set('mot', '');
        // $session->set('dateDebut', NULL);
        // $session->set('dateFin', NULL);

        $categorie = $session->get('categorie');
        $mot = $session->get('mot');
        $dateDebut = $session->get('dateDebut');
        $dateFin = $session->get('dateFin');
        ////////////////////////////////////////////////////////
        $data = $produitRepository->finding($_POST['num_page'], $mot, $categorie, $dateDebut, $dateFin);
        $all = $data['all'];
        $resultat = $data['resultat'];
        $response = new JsonResponse(['all' => $all, 'resultat' => $resultat, ]);
        // $response = new JsonResponse($produits);
        // $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/send_message", name="send_message", methods="POST")
     */
    public function send_message(Request $request, \Swift_Mailer $mailer)
    {
        $email = $_POST['contact_mail'];
        $telephone = $_POST['contact_phone'];
        $message = $_POST['contact_message'];
        if ($email == '' || $telephone == '' || $message == '') {
            return new JsonResponse(['status' => 'error', 'message' => 'Veuillez remplir tous les champs du formulaire', ]);
        }

        $message = (new \Swift_Message('Hello Email'))
            // ->setFrom('jesssewovon@gmail.com')
            // ->setFrom('sewovonjess@gmail.com')
            ->setFrom('holdingMarkom@gmail.com')
            // ->setTo('sewovonjess@gmail.com')
            ->setTo('jesssewovon@gmail.com')
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/message.html.twig',
                    ['email' => $email,
                     'telephone' => $telephone,
                     'message' => $message,
                    ]
                ),
                'text/html'
            )
        ;
        $mailer->send($message);

        $response = new JsonResponse(['status' => 'success', 'message' => 'Message envoyé avec succès', ]);
        // $response = new JsonResponse($produits);
        // $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
