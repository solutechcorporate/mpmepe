<?php

namespace App\Service;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\Util\OperationRequestInitiatorTrait;
use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiPlatformResource
{
    use OperationRequestInitiatorTrait;

    public function __construct(
        private readonly ValidatorInterface $validator,
        ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory
    )
    {
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    /**
     * Validates data returned by the controller if applicable.
     *
     * @param Request $request
     * @throws ValidationException
     */
    public function validate(Request $request): void
    {
        $controllerResult = $request->attributes->get('data');
        $operation = $this->initializeOperation($request);

        if ('api_platform.symfony.main_controller' === $operation?->getController() || $request->attributes->get('_api_platform_disable_listeners')) {
            return;
        }

        if (
            $controllerResult instanceof Response
            || $request->isMethodSafe()
            || $request->isMethod('DELETE')
        ) {
            return;
        }

        if (!$operation || !($operation->canValidate() ?? true)) {
            return;
        }


        $this->validator->validate($controllerResult, $operation->getValidationContext() ?? []);
    }

}

