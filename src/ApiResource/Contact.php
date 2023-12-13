<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\ContactProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(processor: ContactProcessor::class)
    ]
)]
class Contact
{
    #[Assert\NotBlank]
    public string $nomPrenom;

    #[Assert\NotBlank]
    public string $email;

    #[Assert\NotBlank]
    public string $tel;

    #[Assert\NotBlank]
    public string $objet;

    #[Assert\NotBlank]
    public string $message;

    public function getNomPrenom(): string
    {
        return $this->nomPrenom;
    }

    public function setNomPrenom(string $nomPrenom): Contact
    {
        $this->nomPrenom = $nomPrenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Contact
    {
        $this->email = $email;
        return $this;
    }

    public function getTel(): string
    {
        return $this->tel;
    }

    public function setTel(string $tel): Contact
    {
        $this->tel = $tel;
        return $this;
    }

    public function getObjet(): string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): Contact
    {
        $this->objet = $objet;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): Contact
    {
        $this->message = $message;
        return $this;
    }


}