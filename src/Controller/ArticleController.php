<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{

    #[Route('/article', name: 'article')]
    public function index(ArticleRepository $repository, Request $request): Response
    {
        $request->setLocale('fr');
        $articles = $repository->findAll();
        foreach ($articles as $article) {
            $article->contentPreview = substr($article->getContent(), 0, 200);
        }
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/{articleId}', name: 'one_article', requirements: ['articleId' => '\d+'])]
    public function getOneArticle(ArticleRepository $repository, int $articleId): Response
    {
        $article = $repository->find($articleId);
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/create', name: 'article_create')]
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setUser($this->getUser())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('article');
        }
        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
