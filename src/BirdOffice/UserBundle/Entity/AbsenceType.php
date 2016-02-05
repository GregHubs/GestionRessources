<?php

namespace BirdOffice\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;


/**
 * @ORM\Entity
 * @ORM\Table(name="absence_type")
 */
class AbsenceType
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
    private $absenceType;


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
     * Set absenceType
     *
     * @param string $absenceType
     *
     * @return AbsenceType
     */
    public function setAbsenceType($absenceType)
    {
        $this->absenceType = $absenceType;

        return $this;
    }

    /**
     * Get absenceType
     *
     * @return string
     */
    public function getAbsenceType()
    {
        return $this->absenceType;
    }
}
