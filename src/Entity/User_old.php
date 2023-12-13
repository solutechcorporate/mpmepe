<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\AjouterUserAction;
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use App\Utils\Traits\EntityTimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    normalizationContext: ['groups' => ['read:User','read:Entity']],
    denormalizationContext: ['groups' => ['write:User','write:Entity']],
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            controller: AjouterUserAction::class,
            write: false,
            validationContext: ['groups' => ['Default']],
            inputFormats: ['multipart' => ['multipart/form-data']]
        ),
        new Get(
            security: "is_granted('ROLE_USER')"
        ),
//        new Put(
//            processor: UserPasswordHasher::class,
//            security: "is_granted('ROLE_USER')"
//        ),
//        new Patch(
//            processor: UserPasswordHasher::class,
//            security: "is_granted('ROLE_USER')"
//        ),
        new Delete(
            security: "is_granted('ROLE_USER')"
        )
    ]
)]
#[UniqueEntity('email')]
#[ApiFilter(DateFilter::class, properties: ['dateNais', 'dateAjout', 'dateModif'])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'nom', 'prenom', 'sexe', 'adresse', 'typeUser'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'read:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups([
        'read:User',
        'write:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Groups([
        'read:User',
        'read:Location',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank]
    #[Groups(['write:User'])]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups([
        'read:User',
        'write:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:User',
        'write:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 2)]
    #[Groups([
        'read:User',
        'write:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    #[Assert\NotBlank]
    private ?string $sexe = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read:User', 'write:User'])]
    private ?\DateTimeInterface $dateNais = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:User',
        'write:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    #[Assert\NotBlank]
    private ?Pays $pays = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['read:User', 'write:User', 'read:Location'])]
    private ?Quartier $quartier = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:User', 'write:User', 'read:Location'])]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:User',
        'write:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    #[Assert\NotBlank]
    private ?string $tel1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:User', 'write:User', 'read:Avis', 'read:Location', 'read:Messagerie'])]
    private ?string $tel2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:User', 'write:User', 'read:Avis', 'read:Location', 'read:Messagerie'])]
    private ?string $facebook = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'read:User',
        'write:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    private ?string $siteInternet = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:User', 'write:User', 'read:Location'])]
    private ?string $twitter = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:User', 'write:User', 'read:Location'])]
    private ?string $instagram = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:User', 'write:User', 'read:Avis', 'read:Location', 'read:Messagerie'])]
    private ?string $linkedin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:User', 'write:User', 'read:Location'])]
    private ?string $telegram = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'read:User',
        'write:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    private ?string $youtube = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:User', 'write:User', 'read:Location'])]
    private ?string $pinterest = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:User', 'write:User', 'read:Location'])]
    private ?string $vimeo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'read:User',
        'write:User',
        'read:Avis',
        'read:Location',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    private ?string $whatsapp = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:User',
        'write:User',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    #[Assert\NotBlank]
    private ?TypeUser $typeUser = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DataUser::class)]
    private Collection $dataUsers;

    /*
     * Au cas ou l'utilisateur travaille dans une antenne
     */
    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['read:User', 'write:User'])]
    private ?Antenne $antenne = null;

    /*
     * Au cas ou l'utilisateur travaille dans une agence
     */
    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['read:User', 'write:User'])]
    private ?Agence $agence = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:User',
        'read:Avis',
        'read:Messagerie',
        'read:CategorieArticle',
        'read:ArticleConseil',
        'read:Service',
        'read:Pays',
        'read:Bien'
    ])]
    private ?string $photoCodeFichier = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:User'])]
    private ?string $selfiePieceCodeFichier = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: LocationVente::class)]
    private Collection $locationVentes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Avis::class)]
    private Collection $avis;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserLangue::class)]
    private Collection $userLangues;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Messagerie::class)]
    private Collection $messageriesEmetteur;

    #[ORM\OneToMany(mappedBy: 'destinataire', targetEntity: Messagerie::class)]
    private Collection $messageriesDestinataire;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['read:User', 'write:User'])]
    private ?Devise $devise = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Agence::class)]
    private Collection $agencesCreer;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Antenne::class)]
    private Collection $antennes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Arrondissement::class)]
    private Collection $arrondissements;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ArticleConseil::class)]
    private Collection $articleConseils;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AttributBien::class)]
    private Collection $attributBiens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AttributUser::class)]
    private Collection $attributUsers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Bien::class)]
    private Collection $biens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BienCaracteristique::class)]
    private Collection $bienCaracteristiques;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Caracteristique::class)]
    private Collection $caracteristiques;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CategorieArticle::class)]
    private Collection $categorieArticles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Commune::class)]
    private Collection $communes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DataBien::class)]
    private Collection $dataBiens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Departement::class)]
    private Collection $departements;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Devise::class)]
    private Collection $devises;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Langue::class)]
    private Collection $langues;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Pays::class)]
    private Collection $paysCreator;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Quartier::class)]
    private Collection $quartiers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Service::class)]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TypeBien::class)]
    private Collection $typeBiens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TypeBienAttribut::class)]
    private Collection $typeBienAttributs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TypeUser::class)]
    private Collection $typeUsers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TypeUserAttribut::class)]
    private Collection $typeUserAttributs;

    public function __construct()
    {
        $this->dataUsers = new ArrayCollection();
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = false;
        $this->locationVentes = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->userLangues = new ArrayCollection();
        $this->messageriesEmetteur = new ArrayCollection();
        $this->messageriesDestinataire = new ArrayCollection();
        $this->agencesCreer = new ArrayCollection();
        $this->antennes = new ArrayCollection();
        $this->arrondissements = new ArrayCollection();
        $this->articleConseils = new ArrayCollection();
        $this->attributBiens = new ArrayCollection();
        $this->attributUsers = new ArrayCollection();
        $this->biens = new ArrayCollection();
        $this->bienCaracteristiques = new ArrayCollection();
        $this->caracteristiques = new ArrayCollection();
        $this->categorieArticles = new ArrayCollection();
        $this->communes = new ArrayCollection();
        $this->dataBiens = new ArrayCollection();
        $this->departements = new ArrayCollection();
        $this->devises = new ArrayCollection();
        $this->langues = new ArrayCollection();
        $this->paysCreator = new ArrayCollection();
        $this->quartiers = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->typeBiens = new ArrayCollection();
        $this->typeBienAttributs = new ArrayCollection();
        $this->typeUsers = new ArrayCollection();
        $this->typeUserAttributs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
         $this->plainPassword = "null";
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDateNais(): ?\DateTimeInterface
    {
        return $this->dateNais;
    }

    public function setDateNais(?\DateTimeInterface $dateNais): static
    {
        $this->dateNais = $dateNais;

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): static
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTel1(): ?string
    {
        return $this->tel1;
    }

    public function setTel1(string $tel1): static
    {
        $this->tel1 = $tel1;

        return $this;
    }

    public function getTel2(): ?string
    {
        return $this->tel2;
    }

    public function setTel2(?string $tel2): static
    {
        $this->tel2 = $tel2;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): static
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getSiteInternet(): ?string
    {
        return $this->siteInternet;
    }

    public function setSiteInternet(?string $siteInternet): static
    {
        $this->siteInternet = $siteInternet;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): static
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): static
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): static
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getTelegram(): ?string
    {
        return $this->telegram;
    }

    public function setTelegram(?string $telegram): static
    {
        $this->telegram = $telegram;

        return $this;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(?string $youtube): static
    {
        $this->youtube = $youtube;

        return $this;
    }

    public function getPinterest(): ?string
    {
        return $this->pinterest;
    }

    public function setPinterest(?string $pinterest): static
    {
        $this->pinterest = $pinterest;

        return $this;
    }

    public function getVimeo(): ?string
    {
        return $this->vimeo;
    }

    public function setVimeo(?string $vimeo): static
    {
        $this->vimeo = $vimeo;

        return $this;
    }

    public function getWhatsapp(): ?string
    {
        return $this->whatsapp;
    }

    public function setWhatsapp(?string $whatsapp): static
    {
        $this->whatsapp = $whatsapp;

        return $this;
    }

    public function getTypeUser(): ?TypeUser
    {
        return $this->typeUser;
    }

    public function setTypeUser(?TypeUser $typeUser): static
    {
        $this->typeUser = $typeUser;

        return $this;
    }

    /**
     * @return Collection<int, DataUser>
     */
    public function getDataUsers(): Collection
    {
        return $this->dataUsers;
    }

    public function addDataUser(DataUser $dataUser): static
    {
        if (!$this->dataUsers->contains($dataUser)) {
            $this->dataUsers->add($dataUser);
            $dataUser->setUtilisateur($this);
        }

        return $this;
    }

    public function removeDataUser(DataUser $dataUser): static
    {
        if ($this->dataUsers->removeElement($dataUser)) {
            // set the owning side to null (unless already changed)
            if ($dataUser->getUtilisateur() === $this) {
                $dataUser->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getAntenne(): ?Antenne
    {
        return $this->antenne;
    }

    public function setAntenne(?Antenne $antenne): static
    {
        $this->antenne = $antenne;

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): static
    {
        $this->agence = $agence;

        return $this;
    }

    public function getPhotoCodeFichier(): ?string
    {
        return $this->photoCodeFichier;
    }

    public function setPhotoCodeFichier(?string $photoCodeFichier): static
    {
        $this->photoCodeFichier = $photoCodeFichier;

        return $this;
    }

    public function getSelfiePieceCodeFichier(): ?string
    {
        return $this->selfiePieceCodeFichier;
    }

    public function setSelfiePieceCodeFichier(?string $selfiePieceCodeFichier): static
    {
        $this->selfiePieceCodeFichier = $selfiePieceCodeFichier;

        return $this;
    }

    /**
     * @return Collection<int, LocationVente>
     */
    public function getLocationVentes(): Collection
    {
        return $this->locationVentes;
    }

    public function addLocationVente(LocationVente $locationVente): static
    {
        if (!$this->locationVentes->contains($locationVente)) {
            $this->locationVentes->add($locationVente);
            $locationVente->setUser($this);
        }

        return $this;
    }

    public function removeLocationVente(LocationVente $locationVente): static
    {
        if ($this->locationVentes->removeElement($locationVente)) {
            // set the owning side to null (unless already changed)
            if ($locationVente->getUser() === $this) {
                $locationVente->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setUser($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getUser() === $this) {
                $avi->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserLangue>
     */
    public function getUserLangues(): Collection
    {
        return $this->userLangues;
    }

    public function addUserLangue(UserLangue $userLangue): static
    {
        if (!$this->userLangues->contains($userLangue)) {
            $this->userLangues->add($userLangue);
            $userLangue->setUser($this);
        }

        return $this;
    }

    public function removeUserLangue(UserLangue $userLangue): static
    {
        if ($this->userLangues->removeElement($userLangue)) {
            // set the owning side to null (unless already changed)
            if ($userLangue->getUser() === $this) {
                $userLangue->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messagerie>
     */
    public function getMessageriesEmetteur(): Collection
    {
        return $this->messageriesEmetteur;
    }

    public function addMessageriesEmetteur(Messagerie $messageriesEmetteur): static
    {
        if (!$this->messageriesEmetteur->contains($messageriesEmetteur)) {
            $this->messageriesEmetteur->add($messageriesEmetteur);
            $messageriesEmetteur->setEmetteur($this);
        }

        return $this;
    }

    public function removeMessageriesEmetteur(Messagerie $messageriesEmetteur): static
    {
        if ($this->messageriesEmetteur->removeElement($messageriesEmetteur)) {
            // set the owning side to null (unless already changed)
            if ($messageriesEmetteur->getEmetteur() === $this) {
                $messageriesEmetteur->setEmetteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messagerie>
     */
    public function getMessageriesDestinataire(): Collection
    {
        return $this->messageriesDestinataire;
    }

    public function addMessageriesDestinataire(Messagerie $messageriesDestinataire): static
    {
        if (!$this->messageriesDestinataire->contains($messageriesDestinataire)) {
            $this->messageriesDestinataire->add($messageriesDestinataire);
            $messageriesDestinataire->setDestinataire($this);
        }

        return $this;
    }

    public function removeMessageriesDestinataire(Messagerie $messageriesDestinataire): static
    {
        if ($this->messageriesDestinataire->removeElement($messageriesDestinataire)) {
            // set the owning side to null (unless already changed)
            if ($messageriesDestinataire->getDestinataire() === $this) {
                $messageriesDestinataire->setDestinataire(null);
            }
        }

        return $this;
    }

    public function getDevise(): ?Devise
    {
        return $this->devise;
    }

    public function setDevise(?Devise $devise): static
    {
        $this->devise = $devise;

        return $this;
    }

    /**
     * @return Collection<int, Agence>
     */
    public function getAgencesCreer(): Collection
    {
        return $this->agencesCreer;
    }

    public function addAgencesCreer(Agence $agencesCreer): static
    {
        if (!$this->agencesCreer->contains($agencesCreer)) {
            $this->agencesCreer->add($agencesCreer);
            $agencesCreer->setUser($this);
        }

        return $this;
    }

    public function removeAgencesCreer(Agence $agencesCreer): static
    {
        if ($this->agencesCreer->removeElement($agencesCreer)) {
            // set the owning side to null (unless already changed)
            if ($agencesCreer->getUser() === $this) {
                $agencesCreer->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Antenne>
     */
    public function getAntennes(): Collection
    {
        return $this->antennes;
    }

    public function addAntenne(Antenne $antenne): static
    {
        if (!$this->antennes->contains($antenne)) {
            $this->antennes->add($antenne);
            $antenne->setUserCreator($this);
        }

        return $this;
    }

    public function removeAntenne(Antenne $antenne): static
    {
        if ($this->antennes->removeElement($antenne)) {
            // set the owning side to null (unless already changed)
            if ($antenne->getUserCreator() === $this) {
                $antenne->setUserCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Arrondissement>
     */
    public function getArrondissements(): Collection
    {
        return $this->arrondissements;
    }

    public function addArrondissement(Arrondissement $arrondissement): static
    {
        if (!$this->arrondissements->contains($arrondissement)) {
            $this->arrondissements->add($arrondissement);
            $arrondissement->setUser($this);
        }

        return $this;
    }

    public function removeArrondissement(Arrondissement $arrondissement): static
    {
        if ($this->arrondissements->removeElement($arrondissement)) {
            // set the owning side to null (unless already changed)
            if ($arrondissement->getUser() === $this) {
                $arrondissement->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ArticleConseil>
     */
    public function getArticleConseils(): Collection
    {
        return $this->articleConseils;
    }

    public function addArticleConseil(ArticleConseil $articleConseil): static
    {
        if (!$this->articleConseils->contains($articleConseil)) {
            $this->articleConseils->add($articleConseil);
            $articleConseil->setUser($this);
        }

        return $this;
    }

    public function removeArticleConseil(ArticleConseil $articleConseil): static
    {
        if ($this->articleConseils->removeElement($articleConseil)) {
            // set the owning side to null (unless already changed)
            if ($articleConseil->getUser() === $this) {
                $articleConseil->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AttributBien>
     */
    public function getAttributBiens(): Collection
    {
        return $this->attributBiens;
    }

    public function addAttributBien(AttributBien $attributBien): static
    {
        if (!$this->attributBiens->contains($attributBien)) {
            $this->attributBiens->add($attributBien);
            $attributBien->setUser($this);
        }

        return $this;
    }

    public function removeAttributBien(AttributBien $attributBien): static
    {
        if ($this->attributBiens->removeElement($attributBien)) {
            // set the owning side to null (unless already changed)
            if ($attributBien->getUser() === $this) {
                $attributBien->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AttributUser>
     */
    public function getAttributUsers(): Collection
    {
        return $this->attributUsers;
    }

    public function addAttributUser(AttributUser $attributUser): static
    {
        if (!$this->attributUsers->contains($attributUser)) {
            $this->attributUsers->add($attributUser);
            $attributUser->setUser($this);
        }

        return $this;
    }

    public function removeAttributUser(AttributUser $attributUser): static
    {
        if ($this->attributUsers->removeElement($attributUser)) {
            // set the owning side to null (unless already changed)
            if ($attributUser->getUser() === $this) {
                $attributUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bien>
     */
    public function getBiens(): Collection
    {
        return $this->biens;
    }

    public function addBien(Bien $bien): static
    {
        if (!$this->biens->contains($bien)) {
            $this->biens->add($bien);
            $bien->setUser($this);
        }

        return $this;
    }

    public function removeBien(Bien $bien): static
    {
        if ($this->biens->removeElement($bien)) {
            // set the owning side to null (unless already changed)
            if ($bien->getUser() === $this) {
                $bien->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BienCaracteristique>
     */
    public function getBienCaracteristiques(): Collection
    {
        return $this->bienCaracteristiques;
    }

    public function addBienCaracteristique(BienCaracteristique $bienCaracteristique): static
    {
        if (!$this->bienCaracteristiques->contains($bienCaracteristique)) {
            $this->bienCaracteristiques->add($bienCaracteristique);
            $bienCaracteristique->setUser($this);
        }

        return $this;
    }

    public function removeBienCaracteristique(BienCaracteristique $bienCaracteristique): static
    {
        if ($this->bienCaracteristiques->removeElement($bienCaracteristique)) {
            // set the owning side to null (unless already changed)
            if ($bienCaracteristique->getUser() === $this) {
                $bienCaracteristique->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Caracteristique>
     */
    public function getCaracteristiques(): Collection
    {
        return $this->caracteristiques;
    }

    public function addCaracteristique(Caracteristique $caracteristique): static
    {
        if (!$this->caracteristiques->contains($caracteristique)) {
            $this->caracteristiques->add($caracteristique);
            $caracteristique->setUser($this);
        }

        return $this;
    }

    public function removeCaracteristique(Caracteristique $caracteristique): static
    {
        if ($this->caracteristiques->removeElement($caracteristique)) {
            // set the owning side to null (unless already changed)
            if ($caracteristique->getUser() === $this) {
                $caracteristique->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CategorieArticle>
     */
    public function getCategorieArticles(): Collection
    {
        return $this->categorieArticles;
    }

    public function addCategorieArticle(CategorieArticle $categorieArticle): static
    {
        if (!$this->categorieArticles->contains($categorieArticle)) {
            $this->categorieArticles->add($categorieArticle);
            $categorieArticle->setUser($this);
        }

        return $this;
    }

    public function removeCategorieArticle(CategorieArticle $categorieArticle): static
    {
        if ($this->categorieArticles->removeElement($categorieArticle)) {
            // set the owning side to null (unless already changed)
            if ($categorieArticle->getUser() === $this) {
                $categorieArticle->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commune>
     */
    public function getCommunes(): Collection
    {
        return $this->communes;
    }

    public function addCommune(Commune $commune): static
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
            $commune->setUser($this);
        }

        return $this;
    }

    public function removeCommune(Commune $commune): static
    {
        if ($this->communes->removeElement($commune)) {
            // set the owning side to null (unless already changed)
            if ($commune->getUser() === $this) {
                $commune->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DataBien>
     */
    public function getDataBiens(): Collection
    {
        return $this->dataBiens;
    }

    public function addDataBien(DataBien $dataBien): static
    {
        if (!$this->dataBiens->contains($dataBien)) {
            $this->dataBiens->add($dataBien);
            $dataBien->setUser($this);
        }

        return $this;
    }

    public function removeDataBien(DataBien $dataBien): static
    {
        if ($this->dataBiens->removeElement($dataBien)) {
            // set the owning side to null (unless already changed)
            if ($dataBien->getUser() === $this) {
                $dataBien->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Departement>
     */
    public function getDepartements(): Collection
    {
        return $this->departements;
    }

    public function addDepartement(Departement $departement): static
    {
        if (!$this->departements->contains($departement)) {
            $this->departements->add($departement);
            $departement->setUser($this);
        }

        return $this;
    }

    public function removeDepartement(Departement $departement): static
    {
        if ($this->departements->removeElement($departement)) {
            // set the owning side to null (unless already changed)
            if ($departement->getUser() === $this) {
                $departement->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Devise>
     */
    public function getDevises(): Collection
    {
        return $this->devises;
    }

    public function addDevise(Devise $devise): static
    {
        if (!$this->devises->contains($devise)) {
            $this->devises->add($devise);
            $devise->setUser($this);
        }

        return $this;
    }

    public function removeDevise(Devise $devise): static
    {
        if ($this->devises->removeElement($devise)) {
            // set the owning side to null (unless already changed)
            if ($devise->getUser() === $this) {
                $devise->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Langue>
     */
    public function getLangues(): Collection
    {
        return $this->langues;
    }

    public function addLangue(Langue $langue): static
    {
        if (!$this->langues->contains($langue)) {
            $this->langues->add($langue);
            $langue->setUser($this);
        }

        return $this;
    }

    public function removeLangue(Langue $langue): static
    {
        if ($this->langues->removeElement($langue)) {
            // set the owning side to null (unless already changed)
            if ($langue->getUser() === $this) {
                $langue->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pays>
     */
    public function getPaysCreator(): Collection
    {
        return $this->paysCreator;
    }

    public function addPaysCreator(Pays $paysCreator): static
    {
        if (!$this->paysCreator->contains($paysCreator)) {
            $this->paysCreator->add($paysCreator);
            $paysCreator->setUser($this);
        }

        return $this;
    }

    public function removePaysCreator(Pays $paysCreator): static
    {
        if ($this->paysCreator->removeElement($paysCreator)) {
            // set the owning side to null (unless already changed)
            if ($paysCreator->getUser() === $this) {
                $paysCreator->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quartier>
     */
    public function getQuartiers(): Collection
    {
        return $this->quartiers;
    }

    public function addQuartier(Quartier $quartier): static
    {
        if (!$this->quartiers->contains($quartier)) {
            $this->quartiers->add($quartier);
            $quartier->setUser($this);
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): static
    {
        if ($this->quartiers->removeElement($quartier)) {
            // set the owning side to null (unless already changed)
            if ($quartier->getUser() === $this) {
                $quartier->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setUser($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getUser() === $this) {
                $service->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeBien>
     */
    public function getTypeBiens(): Collection
    {
        return $this->typeBiens;
    }

    public function addTypeBien(TypeBien $typeBien): static
    {
        if (!$this->typeBiens->contains($typeBien)) {
            $this->typeBiens->add($typeBien);
            $typeBien->setUser($this);
        }

        return $this;
    }

    public function removeTypeBien(TypeBien $typeBien): static
    {
        if ($this->typeBiens->removeElement($typeBien)) {
            // set the owning side to null (unless already changed)
            if ($typeBien->getUser() === $this) {
                $typeBien->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeBienAttribut>
     */
    public function getTypeBienAttributs(): Collection
    {
        return $this->typeBienAttributs;
    }

    public function addTypeBienAttribut(TypeBienAttribut $typeBienAttribut): static
    {
        if (!$this->typeBienAttributs->contains($typeBienAttribut)) {
            $this->typeBienAttributs->add($typeBienAttribut);
            $typeBienAttribut->setUser($this);
        }

        return $this;
    }

    public function removeTypeBienAttribut(TypeBienAttribut $typeBienAttribut): static
    {
        if ($this->typeBienAttributs->removeElement($typeBienAttribut)) {
            // set the owning side to null (unless already changed)
            if ($typeBienAttribut->getUser() === $this) {
                $typeBienAttribut->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeUser>
     */
    public function getTypeUsers(): Collection
    {
        return $this->typeUsers;
    }

    public function addTypeUser(TypeUser $typeUser): static
    {
        if (!$this->typeUsers->contains($typeUser)) {
            $this->typeUsers->add($typeUser);
            $typeUser->setUser($this);
        }

        return $this;
    }

    public function removeTypeUser(TypeUser $typeUser): static
    {
        if ($this->typeUsers->removeElement($typeUser)) {
            // set the owning side to null (unless already changed)
            if ($typeUser->getUser() === $this) {
                $typeUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeUserAttribut>
     */
    public function getTypeUserAttributs(): Collection
    {
        return $this->typeUserAttributs;
    }

    public function addTypeUserAttribut(TypeUserAttribut $typeUserAttribut): static
    {
        if (!$this->typeUserAttributs->contains($typeUserAttribut)) {
            $this->typeUserAttributs->add($typeUserAttribut);
            $typeUserAttribut->setUser($this);
        }

        return $this;
    }

    public function removeTypeUserAttribut(TypeUserAttribut $typeUserAttribut): static
    {
        if ($this->typeUserAttributs->removeElement($typeUserAttribut)) {
            // set the owning side to null (unless already changed)
            if ($typeUserAttribut->getUser() === $this) {
                $typeUserAttribut->setUser(null);
            }
        }

        return $this;
    }
}
