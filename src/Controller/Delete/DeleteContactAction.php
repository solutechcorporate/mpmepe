<?php

namespace App\Controller\Delete;

use App\Entity\Contact;
use App\Service\ControlDeletionEntityService;
use ArrayObject;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class DeleteContactAction extends AbstractController
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

        if ($request->attributes->get('data') instanceof Contact) {
            /** @var Contact $contact */
            $contact = $request->attributes->get('data');

            $this->controlDeletionEntityService->controlDeletion($contact);

            // On retourne null
            $data = null;
        }

        return $data;
    }
}
