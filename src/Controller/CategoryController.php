<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]

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

    #[Route('/category/{categoryId}/edit', name: 'category_update', requirements: ['categoryId' => '\d+'])]
    public function update(Request $request,CategoryRepository $repository, int $categoryId, EntityManagerInterface $em): Response
    {
        $category = $repository->find($categoryId);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/update.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/{categoryId}/delete', name: 'category_delete', requirements: ['categoryId' => '\d+'])]
    public function delete(CategoryRepository $categoryRepository, ArticleRepository $articleRepository, int $categoryId, EntityManagerInterface $em): Response
    {
        $category = $categoryRepository->find($categoryId);
        $articles = $articleRepository->findBy(["category" => $category]);
        if (empty($articles)) {
            $em->remove($category);
            $em->flush();
        }
        foreach($articles as $article) 
        {
            $article->setCategory(null);
            $em->persist($article);
        }

        $em->remove($category);
        $em->flush();
     
        return $this->redirectToRoute('app_category');
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
