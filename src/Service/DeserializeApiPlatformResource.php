<?php

namespace App\Service;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

class DeserializeApiPlatformResource
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer,
        private readonly SerializerContextBuilderInterface $serializerContextBuilder,
        private ?TranslatorInterface $translator = null
    )
    {
        if (null === $this->translator) {
            $this->translator = new class() implements TranslatorInterface, LocaleAwareInterface {
                use TranslatorTrait;
            };
            $this->translator->setLocale($this->translator->getLocale());
        }
    }

    public function deserialize(Request $request, array $payload): bool
    {
        // Récupération des données brutes du corps de la requête
        $requestPostContent = json_encode($payload);

        $method = $request->getMethod();
        $attributes = RequestAttributesExtractor::extractAttributes($request);

        $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
        $format = 'json';
        $data = $request->attributes->get('data');

        // On vérifie si la ressource existe déjà dans la base de données
        if (!is_object($data)) {
            if ($request->request->get('resourceId') != null) {
                $data = $this->entityManager
                    ->getRepository($context['resource_class'])
                    ->find($request->request->get('resourceId'));
            }
        }

        if ($data !== null && $method === 'POST') {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $data;
        }

        try {
            $request->attributes->set(
                'data',
                $this->serializer->deserialize($requestPostContent, $context['resource_class'], $format, $context)
            );

            return true;

        } catch (PartialDenormalizationException $e) {
            $violations = new ConstraintViolationList();

            foreach ($e->getErrors() as $exception) {
                if (!$exception instanceof NotNormalizableValueException) {
                    continue;
                }

                $message = (new Type($exception->getExpectedTypes() ?? []))->message;
                $parameters = [];

                if ($exception->canUseMessageForUser()) {
                    $parameters['hint'] = $exception->getMessage();
                }

                $violations->add(new ConstraintViolation($this->translator->trans($message, ['{{ type }}' => implode('|', $exception->getExpectedTypes() ?? [])], 'validators'), $message, $parameters, null, $exception->getPath(), null, null, (string) $exception->getCode()));
            }

            if (0 !== \count($violations)) {
                throw new ValidationException($violations);
            }

            return false;
        }
    }

}
