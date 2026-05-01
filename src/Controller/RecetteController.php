<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecetteController extends AbstractController
{
    #[Route('/recette', name: 'recette_index')]
    public function index(RecetteRepository $recetteRepository): Response
    {
        return $this->render('recette/index.html.twig', [
            'recettes' => $recetteRepository->findAll(),
        ]);
    }

    #[Route('/recette/new', name: 'recette_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recette->setDateCreation(new \DateTime());
            $recette->setPubliee(true);

            $em->persist($recette);
            $em->flush();

            return $this->redirectToRoute('recette_index');
        }

        return $this->render('recette/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}