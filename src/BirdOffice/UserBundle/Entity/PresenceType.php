<?php

namespace BirdOffice\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;


/**
 * @ORM\Entity
 * @ORM\Table(name="presence_type")
 */
class PresenceType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set presenceType
     *
     * @param string $presenceType
     *
     * @return PresenceType
     */
    public function setPresenceType($presenceType)
    {
        $this->presenceType = $presenceType;

        return $this;
    }

    /**
     * Get presenceType
     *
     * @return string
     */
    public function getPresenceType()
    {
        return $this->presenceType;
    }
}
