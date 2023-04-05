<?php

namespace App\Controller;

use App\Service\EntryDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    // Get articles
    #[Route('/', name: 'app_api_articles_get', methods: ['GET'])]
    public function getArticles(ArticleRepository $articleRepository): JsonResponse
    {
        try {

            $articles = $articleRepository->findAll();

            return $this->json($articles, 200, [], ['groups' => ['article-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get one articles
    #[Route('/{id}', name: 'app_api_articles_get_one', methods: ['GET'])]
    public function getArticleById(ArticleRepository $articleRepository, int $id): JsonResponse
    {
        try {

            $article = $articleRepository->findOneBy(['id' => $id]);

            if (!$article) {
                return $this->json([
                    'error' => 'Article not found'
                ], 404);
            }

            return $this->json($article, 200, [], ['groups' => ['article-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Create article
    #[Route('/create', name: 'app_api_articles_post', methods: ['POST'])]
    public function createArticle(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ArticleRepository $articleRepository, EntryDataService $entryDataService, ValidatorInterface $validator): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $article = new Article();
            $article = $entryDataService->defineKeysInEntity($content, $article, $em);
            if ($article === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity creation'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($article);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $articleRepository->save($article, true);
            return $this->json($article, 201, [], ['groups' => ['article-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage()
            ], 500);

        }
    }

    // Update article
    #[Route('/{id}', name: 'app_api_article_update', methods: ['PATCH'])]
    public function updateArticle(int $id, Request $request, EntryDataService $entryDataService, EntityManagerInterface $em, ArticleRepository $articleRepository, ValidatorInterface $validator): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $articleToUpdate = $articleRepository->findOneBy(['id' => $id]);
            if (!$articleToUpdate) {
                return $this->json([
                    'error' => 'Article not found'
                ], 404);
            }

            $articleToUpdate = $entryDataService->defineKeysInEntity($content, $articleToUpdate, $em);
            if ($articleToUpdate === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity modification'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($articleToUpdate);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $articleRepository->save($articleToUpdate, true);
            return $this->json($articleToUpdate, 200, [], ['groups' => ['article-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete articles
    #[Route('/many', name: 'app_api_article_delete_many', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function deleteArticles(Request $request, EntityManagerInterface $entityManager, ArticleRepository $articleRepository): Response
    {
        try {

            $data = json_decode($request->getContent(), true);
            $ids = $data['id'] ?? [];

            if (empty($ids)) {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }

            $articles = $articleRepository->findBy(['id' => $ids]);

            if (empty($articles) || count($ids) !== count($articles)) {
                return $this->json([
                    'error' => 'One or more article(s) are not found'
                ], 404);
            }

            foreach ($articles as $article) {
                $entityManager->remove($article);
            }

            $entityManager->flush();

            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }  

    // Clear articles
    #[Route('/clear', name: 'app_api_article_delete_all', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function clearArticles(EntityManagerInterface $entityManager, ArticleRepository $articleRepository): Response
    {
        try {

            $articles = $articleRepository->findAll();

            foreach ($articles as $article) {
                $entityManager->remove($article);
            }

            $entityManager->flush();
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete article
    #[Route('/{id}', name: 'app_api_article_delete', methods: ['DELETE'])]
    public function deleteArticle(int $id, ArticleRepository $articleRepository): Response
    {
        try {

            $articleToDelete = $articleRepository->findOneBy(['id' => $id]);

            if (!$articleToDelete) {
                return $this->json([
                    'error' => 'Article not found'
                ], 404);
            }

            $articleRepository->remove($articleToDelete, true);
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    } 
}
