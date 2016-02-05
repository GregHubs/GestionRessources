<?php

namespace BirdOffice\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;


/**
 * @ORM\Entity
 * @ORM\Table(name="day")
 * @ORM\Entity(repositoryClass="BirdOffice\UserBundle\Entity\DayRepository")
 */
class Day
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="BirdOffice\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="BirdOffice\UserBundle\Entity\AbsenceType")
     * @ORM\JoinColumn(name="absenceType", referencedColumnName="id")
     */
    private $absenceType;

    /**
     * @ORM\ManyToOne(targetEntity="BirdOffice\UserBundle\Entity\PresenceType")
     * @ORM\JoinColumn(name="presenceType", referencedColumnName="id")
     */
    private $presenceType;

    /**
     * @ORM\Column(type="datetime", name="start_date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", name="end_date")
     */
    private $endDate;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $hours;
    

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
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Day
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Day
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set user
     *
     * @param \BirdOffice\UserBundle\Entity\User $user
     *
     * @return Day
     */
    public function setUser(\BirdOffice\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \BirdOffice\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set absenceType
     *
     * @param \BirdOffice\UserBundle\Entity\AbsenceType $absenceType
     *
     * @return Day
     */
    public function setAbsenceType(\BirdOffice\UserBundle\Entity\AbsenceType $absenceType = null)
    {
        $this->absenceType = $absenceType;

        return $this;
    }

    /**
     * Get absenceType
     *
     * @return \BirdOffice\UserBundle\Entity\AbsenceType
     */
    public function getAbsenceType()
    {
        return $this->absenceType;
    }

    /**
     * Set presenceType
     *
     * @param \BirdOffice\UserBundle\Entity\PresenceType $presenceType
     *
     * @return Day
     */
    public function setPresenceType(\BirdOffice\UserBundle\Entity\PresenceType $presenceType = null)
    {
        $this->presenceType = $presenceType;

        return $this;
    }

    /**
     * Get presenceType
     *
     * @return \BirdOffice\UserBundle\Entity\PresenceType
     */
    public function getPresenceType()
    {
        return $this->presenceType;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Day
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set hours
     *
     * @param integer $hours
     *
     * @return Day
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return integer
     */
    public function getHours()
    {
        return $this->hours;
    }
}
