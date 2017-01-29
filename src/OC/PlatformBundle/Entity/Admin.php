<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

class Admin
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="Compte", type="string", length=10)
     * @Assert\NotBlank
     * @Assert\EqualTo(value="hypairball",  message="Le nom de compte est incorrect.")
     */
    private $Compte= '';

    /**
     * @ORM\Column(name="Passe", type="string", length=9)
     * @Assert\NotBlank
     * @Assert\EqualTo(value="jf65comte", message="Mot de passe incorrect")
     */
    private $Passe= '';

    /**
     * @return string
     */
    public function getCompte()
    {
        return $this->Compte;
    }

    /**
     * @param string $Compte
     * @return Admin
     */
    public function setCompte($Compte)
    {
        $this->Compte = $Compte;
        return $this;
    }

    /**
     * @return string
     */
    public function getPasse()
    {
        return $this->Passe;
    }

    /**
     * @param string $Passe
     * @return Admin
     */
    public function setPasse($Passe)
    {
        $this->Passe = $Passe;
        return $this;
    }



}
