<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Service\RequestService;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    // Get articles
    #[Route('/', name: 'app_api_article_get', methods: ['GET'])]
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
    #[Route('/{id}', name: 'app_api_article_get_one', methods: ['GET'])]
    public function getArticleById(ArticleRepository $articleRepository, int $id): JsonResponse
    {
        try {
            $article = $articleRepository->find($id);

            if (!$article) {
                return $this->json([
                    'error' => 'Article not found'
                ], 404);
            }

            return $this->json($article, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Create article
    #[Route('/create', name: 'app_api_article_post', methods: ['POST'])]
    public function createArticle(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }

            $requiredFields = ['title', 'content', 'id_user'];

            // Vérification de la présence de tous les champs requis
            foreach ($requiredFields as $field) {
                if (!isset($content[$field])) {
                    return $this->json([
                        'error' => "Missing field '$field'"
                    ], 400);
                }
            }

            // Vérification des types des champs requis
            if (!is_string($content['title']) || !is_string($content['content']) || !is_numeric($content['id_user'])) {
                return $this->json([
                    'error' => 'One or more filled-in field(s) has/have a wrong type'
                ], 400);
            }

            $article = $serializer->deserialize($request->getContent(), Article::class, 'json');

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->json($article, 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Update article
    #[Route('/{id}', name: 'app_api_article_update', methods: ['PATCH'])]
    public function updateArticle(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        try {
            $article = $entityManager->getRepository(Article::class)->find($id);

            // Vérification de l'existence de l'article
            if ($article === null) {
                return $this->json([
                    'error' => 'Article not found'
                ], 404);
            }

            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }

            foreach ($content as $key => $value) {
                // Vérification de l'existence de la propriété dans l'objet Article
                if (!property_exists(Article::class, $key)) {
                    return $this->json([
                        'error' => "Unknown property '$key'"
                    ], 400);
                }

                $setter = 'set' . ucfirst($key);

                // Vérification de l'existence de la méthode setter pour la propriété
                if (!method_exists(Article::class, $setter)) {
                    return $this->json([
                        'error' => "No setter found for property '$key'"
                    ], 500);
                }

                // Appel de la méthode setter pour modifier la propriété
                $article->$setter($value);
            }

            $entityManager->flush();

            return $this->json($article, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Delete articles
    #[Route('/many', name: 'app_api_article_delete_many', methods: ['DELETE'])]
    public function deleteArticles(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $ids = $data['id'] ?? [];
    
            if (empty($ids)) {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }
    
            $articles = $entityManager->getRepository(Article::class)->findBy([
                'id' => $ids
            ]);
    
            if (empty($articles)) {
                return $this->json([
                    'error' => 'Users not found'
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
    public function clearArticles(EntityManagerInterface $entityManager): Response
    {
        try {
            $articleRepository = $entityManager->getRepository(Article::class);
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
    public function deleteArticle(Article $article = null, EntityManagerInterface $entityManager): Response
    {
        try {
            if (!$article) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            $entityManager->remove($article);
            $entityManager->flush();

            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    } 
}
