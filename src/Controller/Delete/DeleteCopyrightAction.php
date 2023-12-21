<?php

namespace App\Controller\Delete;

use App\Entity\Copyright;
use App\Service\ControlDeletionEntityService;
use ArrayObject;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class DeleteCopyrightAction extends AbstractController
{
    public function __construct(
        private ControlDeletionEntityService $controlDeletionEntityService
    )
    {
    }

    /**
     * @throws ReflectionException
     */
    public function __invoke(Request $request): object|null
    {
        $data = new ArrayObject([
            'message' => "Impossible de supprimer la ressource."
        ]);

        if ($request->attributes->get('data') instanceof Copyright) {
            /** @var Copyright $copyright */
            $copyright = $request->attributes->get('data');

            $this->controlDeletionEntityService->controlDeletion($copyright);

            // On retourne null
            $data = null;
        }

        return $data;
    }
}
