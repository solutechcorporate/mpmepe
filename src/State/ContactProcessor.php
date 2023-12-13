<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\EmailSmsServices;

class ContactProcessor implements ProcessorInterface
{
    public function __construct(private EmailSmsServices $emailSmsServices)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $obj = new \stdClass();
        $obj->message = "L'email a été envoyé avec succès";

        // Code pour envoyer le mail
        $dataEnvoyeMail = [
            'nomPrenom' => $data->getNomPrenom(),
            'telephone' => $data->getTel(),
            'emailForm' => $data->getEmail(),
            'objet' => $data->getObjet(),
            'message' => $data->getMessage(),
        ];

        $this->emailSmsServices->sendEmail(
            ['sylvestrehonfo4@gmail.com', 'miramaralingo2013@gmail.com'],
            'email_templates/contact.html.twig',
            $dataEnvoyeMail['objet'],
            $dataEnvoyeMail['emailForm'],
            $dataEnvoyeMail['nomPrenom'],
            $dataEnvoyeMail
        );

        return $obj;
    }

}
