<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
            $file = $form->get('image')->getData();
            if ($file) {
                $filename = uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'), 
                        $filename
                    );
                } catch (FileException $e) {
        
                }
                $article->setImage($filename);
            }
            //$article = $form->getData();
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

    #[Route('/article/{articleId}/edit', name: 'article_update', requirements: ['articleId' => '\d+'])]
    public function update(Request $request,ArticleRepository $repository, int $articleId, EntityManagerInterface $em, Security $security): Response
    {
        $article = $repository->find($articleId);
        $userLoginId = $security->getUser()->getId();
        $userArticleId = $article->getUser()->getId();
        if ($userLoginId === $userArticleId) {
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $file = $form->get('image')->getData();
            if ($file) 
            {
                $oldFileName = $article->getImage();
                $filename = uniqid().'.'.$file->guessExtension();
                try 
                {
                    $file->move(
                        $this->getParameter('images_directory'), 
                        $filename
                    );

                    if ($oldFileName) {
                        $oldFilepath = $this->getParameter('images_directory') . '/' . $oldFileName;
                        if (file_exists($oldFilepath)) {
                            unlink($oldFilepath); 
                        }
                    }
                    $article->setImage($filename);
                } 
                catch (FileException $e) 
                {
                    
                }
                
            }
                $article->setUpdatedAt(new \DateTimeImmutable());
                $em->persist($article);
                $em->flush();
                $this->addFlash("success", "modification réussi");
                return $this->redirectToRoute('article');
            }
            
            return $this->render('article/update.html.twig', [
                'article' => $article,
                'form' => $form->createView(),
            ]);
        }

        $this->addFlash("danger", "vous n'êtes pas propriétaire de l'article");
        return $this->redirectToRoute('article');
    }

    #[Route('/article/{articleId}/delete', name: 'article_delete', requirements: ['articleId' => '\d+'])]
    public function delete(ArticleRepository $articleRepository, int $articleId, EntityManagerInterface $em, Security $security): Response
    {
        $article = $articleRepository->find($articleId);
        $userLoginId = $security->getUser()->getId();
        $userArticleId = $article->getUser()->getId();
        if ($userLoginId === $userArticleId) {
            $file = $article->getImage();
            if($file) {

                $filepath = $this->getParameter('images_directory') . '/' . $file;
                if (file_exists($filepath)) {
                    unlink($filepath); 
                }
                
            }
            $em->remove($article);
            $em->flush();
            $this->addFlash("success", "suppression réussi");
        }
        else {
            $this->addFlash("danger", "suppression echouer");
        }
        return $this->redirectToRoute('article');
    }
}
