<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findByVendeur($this->getUser()),
        ]);
    }

    /**
     * @Route("/all", name="produit_all", methods={"GET"})
     */
    public function produit_all(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/tout.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ///////////////PRODUIT UPLOAD
            $file = $produit->getPhoto();
            if (!empty($form["photo"]->getData())) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                // moves the file to the directory where logos are stored
                $file->move(
                    $this->getParameter('upload_image_directory'),
                    $fileName
                );
                $produit->setPhoto($fileName);
            }else{
                $produit->setPhoto('');
            }
            //////////////////////////////
            $produit->setDatePublication(new \Datetime('now'));
            $produit->setVendeur($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $photo_ancienne = $produit->getPhoto();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ////////////////////////PRODUIT UPLOAD
            if (!empty($form["photo"]->getData())) {
                ////////////DELETE OLD IMAGE
                if($photo_ancienne != '' && file_exists($this->getParameter('upload_image_directory').'/'.$photo_ancienne))
                    unlink($this->getParameter('upload_image_directory').'/'.$photo_ancienne);
                /////////////////////////////////////////////////////
                $file = $produit->getPhoto();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // moves the file to the directory where logos are stored
                $file->move(
                    $this->getParameter('upload_image_directory'),
                    $fileName
                );
                //unlink($this->getParameter('upload_image_directory').'/'.$photo_ancienne);
                $produit->setPhoto($fileName);
            }else{
                $produit->setPhoto($photo_ancienne);
            }
            ///////////////////////////////////////
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }
}
