<?php

namespace App\Controller\Delete;

use App\Entity\Header;
use App\Service\ControlDeletionEntityService;
use ArrayObject;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class DeleteHeaderAction extends AbstractController
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

        if ($request->attributes->get('data') instanceof Header) {
            /** @var Header $header */
            $header = $request->attributes->get('data');

            $this->controlDeletionEntityService->controlDeletion($header);

            // On retourne null
            $data = null;
        }

        return $data;
    }
}
