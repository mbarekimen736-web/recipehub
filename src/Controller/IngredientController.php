<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredients', name: 'ingredient_index', methods: ['GET'])]
    public function index(IngredientRepository $repo): Response
    {
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $repo->findAll()
        ]);
    }

    #[Route('/ingredients/new', name: 'ingredient_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ingredient);
            $em->flush();

            return $this->redirectToRoute('ingredient_index');
        }

        return $this->render('ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredients/{id}/edit', name: 'ingredient_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Ingredient $ingredient, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('ingredient_index');
        }

        return $this->render('ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredients/{id}/delete', name: 'ingredient_delete', methods: ['POST'])]
    public function delete(Request $request, Ingredient $ingredient, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ingredient->getId(), $request->request->get('_token'))) {
            $em->remove($ingredient);
            $em->flush();
        }

        return $this->redirectToRoute('ingredient_index');
    }
}