<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{categoryId}', name: 'show_category', requirements: ['categoryId' => '\d+'])]
    public function showOneCategory(CategoryRepository $repository,int $categoryId): Response
    {
        $category = $repository->find($categoryId);
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/category/{categoryId}/edit', name: 'category_update')]
    public function update(CategoryRepository $repository, int $id): Response
    {
        $category = $repository->find($id);


        return $this->render('category/index.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/category/create', name: 'category_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('app_category');
        }
        return $this->render('category/create.html.twig', [
            'category' => $form->createView(),
        ]);
    }




}
