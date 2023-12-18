<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Contact;
use App\Entity\ContactValeurDemande;
use App\Entity\ValeurDemande;
use App\Repository\FilesRepository;
use App\Service\FileUploader;
use App\Service\RandomStringGeneratorServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class AjouterContactAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploader $fileUploader,
        private RandomStringGeneratorServices $randomStringGeneratorServices,
        private FilesRepository $filesRepository,
        private SerializerInterface $serializer,
        private SerializerContextBuilderInterface $serializerContextBuilder,
    )
    {
    }

    public function __invoke(Request $request): \ArrayObject
    {
        $data = new \ArrayObject([
            'message' => "Impossible de désérialiser les données."
        ]);

        if ($request->attributes->get('data') instanceof Contact) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Contact $contact */
            $contact = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $this->entityManager->persist($contact);
                $this->entityManager->flush();
                $this->entityManager->refresh($contact);

                // Enregistrement des valeurs demandes
                if (isset($request->request->all()['valeurDemande'])) {
                    foreach ($request->request->all()['valeurDemande'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $valeurDemandeId = explode('/', $d);
                        $valeurDemandeId = $valeurDemandeId[(count($valeurDemandeId) - 1)];

                        $valeurDemande = $this->entityManager
                            ->getRepository(ValeurDemande::class)
                            ->find($valeurDemandeId)
                        ;

                        $contactValeurDemande = (new ContactValeurDemande())
                            ->setValeurDemande($valeurDemande)
                            ->setContact($contact)
                        ;

                        $this->entityManager->persist($contactValeurDemande);
                    }

                    $this->entityManager->flush();
                } // Fin enregistrement des valeurs demandes

            } // resourceId n'existe pas

            // Modification des informations du contact
            if ($request->request->get('resourceId')) {
                $contact->setId((int) $request->request->get('resourceId'));

                $existContact = $this->entityManager->getRepository(Contact::class)
                    ->findOneBy(
                        [
                            'id' => $contact->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($contact, 'json', []);

                // Remplacement des valeurs dans $entitySerialise
                $entitySerialise = json_decode($entitySerialise, true);
                foreach ($entitySerialise as $k => $v) {
                    if (\gettype($v) === 'boolean') {
                        $entitySerialise[$k] = $v === true ? "1" : "0";
                    }

                    if (\gettype($v) === 'integer' || \gettype($v) === 'double') {
                        $entitySerialise[$k] = (string) $v;
                    }
                }
                $entitySerialise = json_encode($entitySerialise);

                if ($existContact) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existContact;
                    $contact = $this->serializer->deserialize($entitySerialise, Contact::class, 'json', $context);
                }

                $this->entityManager->flush();
                $this->entityManager->refresh($contact);

                // Enregistrement des valeurs demandes
                if (isset($request->request->all()['valeurDemande'])) {
                    foreach ($request->request->all()['valeurDemande'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $valeurDemandeId = explode('/', $d);
                        $valeurDemandeId = $valeurDemandeId[(count($valeurDemandeId) - 1)];

                        $valeurDemande = $this->entityManager
                            ->getRepository(ValeurDemande::class)
                            ->find($valeurDemandeId)
                        ;

                        $existContactValeurDemande = $this->entityManager
                            ->getRepository(ContactValeurDemande::class)
                            ->findOneBy(
                                [
                                    'contact' => $contact,
                                    'valeurDemande' => $valeurDemande
                                ]
                            )
                        ;

                        if ($existContactValeurDemande === null) {
                            $existContactValeurDemande = (new ContactValeurDemande())
                                ->setValeurDemande($valeurDemande)
                                ->setContact($contact)
                            ;

                            $this->entityManager->persist($existContactValeurDemande);
                        }
                    }

                    $this->entityManager->flush();
                } // Fin enregistrement des valeurs demandes

            } // resourceId existe

            // On retourne un objet ArrayObject
            $data = new \ArrayObject([
                'contact' => $contact
            ]);
        }

        return $data;
    }

}
