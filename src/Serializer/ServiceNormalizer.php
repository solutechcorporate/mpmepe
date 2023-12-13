<?php

namespace App\Serializer;

use App\Entity\Service;
use App\Repository\FilesRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class ServiceNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'SERVICE_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private FilesRepository $filesRepository,
        private ParameterBagInterface $parameterBag
    )
    {
    }

    /**
     * @param Service $object
     * @param string|null $format
     * @param array $context
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;

        $fichierImageEntete = $this->filesRepository->findOneBy(
            [
                'referenceCode' => $object->getCodeFichierImageEntete()
            ]
        );

        $serverUrl = $this->parameterBag->get('serverUrl');

        if ($fichierImageEntete !== null) {
            $object->urlImageEntete = $serverUrl.$fichierImageEntete->getLocation().$fichierImageEntete->getFilename();
        } else {
            $object->urlImageEntete = null;
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Service;
    }

}
