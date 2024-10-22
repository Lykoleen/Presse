<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles', name: 'articles_')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(ArticleRepository $articleRepository): Response
    {
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    #[Route('/create', name: 'create')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
    
            $article = $form->getData();
            $article->setStatus('DRAFT');

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'L\'article a été créé');

            return $this->redirectToRoute('articles_list');
        }
        return $this->render('articles/edit.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(): Response
    {
        $this->addFlash('success', 'L\'article a bien été supprimé.');

        return $this->redirectToRoute('articles_list');
    }
}
