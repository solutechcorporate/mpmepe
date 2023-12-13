<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Egulias\EmailValidator\EmailValidator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Egulias\EmailValidator\Validation\RFCValidation;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;

//use Twilio\Exceptions\TwilioException;
//use Twilio\Rest\Client;

class EmailSmsServices
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * Envoie un email
     *
     * @param string|array $to
     * @param string $html
     * @param string $sujet
     * @param string $from
     * @param string $fromName
     * @param array<int,string> $data
     * @param array $fichierJoint
     * @return void
     */
    public function sendEmail(
        string|array $to,
        string $html,
        string $sujet,
        string $from = null,
        string $fromName = '',
        array $data = [],
        array $fichierJoint = [],
    ): void
    {
        $validator = new EmailValidator();
        $multipleValidations = new MultipleValidationWithAnd([
            new RFCValidation(),
            new DNSCheckValidation()
        ]);

        $adressesValides = [];

        if (\is_string($to)) {
            if ($validator->isValid($to, $multipleValidations) === true) {
                $adressesValides[] = $to;
            }
        }

        if (\is_array($to)) {
            foreach ($to as $adresse) {
                if ($validator->isValid($adresse, $multipleValidations) === true) {
                    $adressesValides[] = $adresse;
                }
            }
        }

        if (count($adressesValides) > 0) {
            $templatedEmail = new TemplatedEmail();

            if ($from !== null && trim($from) !== '') {
                $address = new Address($from, $fromName);
                $templatedEmail
                    ->from($address)
                    ->replyTo($address)
                ;
            } else {
                $templatedEmail->from("aucune_adresse_email@gmail.com");
            }

            $templatedEmail
                ->to(...$adressesValides)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject($sujet)
                //            ->text('Sending emails is fun again!')
                ->htmlTemplate($html)
                ->context($data);

            // Permet de joindre des fichiers à envoyer par mail
            if (count($fichierJoint) > 0) {
                foreach ($fichierJoint as $cheminCompletDuFichier) {
                    $templatedEmail->attachFromPath($cheminCompletDuFichier);
                }
            }

            $this->mailer->send($templatedEmail);
        }
    }

    //    public function sendSms($to, $body)
    //    {
    //        $twilio_number = "+12349013620";
    //        $twilio_ID = "SGE ADEOTI";
    //
    //        try {
    //            $client = new Client(AppParameterConstants::TWILIO_SID, AppParameterConstants::TWILIO_TOKEN);
    //            $message = $client->messages->create(
    //            // Where to send a text message (your cell phone?)
    //                '+' . $to,
    //                [
    //                    'from' => $twilio_ID,
    //                    'body' => $body
    //                ]
    //            );
    //        } catch (TwilioException $e) {
    //            return $e;
    //        }
    //        return $message;
    //    }
}
