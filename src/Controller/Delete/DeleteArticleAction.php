<?php

namespace App\Controller\Delete;

use App\Entity\Article;
use App\Service\ControlDeletionEntityService;
use ArrayObject;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class DeleteArticleAction extends AbstractController
{
    public function __construct(
        private ControlDeletionEntityService $controlDeletionEntityService
    )
    {
    }

    /**
     * @throws ReflectionException
     */
    public function __invoke(Request $request): ArrayObject|null
    {
        $data = new ArrayObject([
            'message' => "Impossible de supprimer la ressource."
        ]);

        if ($request->attributes->get('data') instanceof Article) {
            /** @var Article $article */
            $article = $request->attributes->get('data');

            $this->controlDeletionEntityService->controlDeletion($article);

            // On retourne null
            $data = null;
        }

        return $data;
    }
}
