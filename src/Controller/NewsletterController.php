<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Repository\NewsletterRepository;

use App\Entity\Newsletter;

class NewsletterController extends AbstractController
{
    /**
     * @Route("/newsletter", name="newsletter", methods="POST")
     */
    public function index(NewsletterRepository $newsletterRepository)
    {
    	$email = $_POST['email'];
    	$newsletter = $newsletterRepository->findOneByEmail($email);
    	if (!is_null($newsletter)) {
    		return new JsonResponse(['status' => 'error', 'message' => "Email déjà existant"]);
    	}
    	$newsletter = new Newsletter();
    	$newsletter->setEmail($email);
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($newsletter);
    	$em->flush();
        ////////////////////////////////////////////////////////
        $response = new JsonResponse(['status' => 'success', 'message' => "Souscription réussie"]);

        return $response;
    }
}
