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
 * @ORM\Entity(repositoryClass="BirdOffice\UserBundle\Repository\DayRepository")
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
     * @ORM\JoinColumn(name="absence_type", referencedColumnName="id", nullable=true)
     */
    private $absenceType;

    /**
     * @ORM\ManyToOne(targetEntity="BirdOffice\UserBundle\Entity\PresenceType")
     * @ORM\JoinColumn(name="presence_type", referencedColumnName="id", nullable=true)
     */
    private $presenceType;

    /**
     * @ORM\Column(type="datetime", name="start_date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", name="end_date", nullable=true)
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
     * @ORM\Column(name="is_validated", type="boolean")
     */
    private $isValidated;
    

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
    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate ? clone $startDate : null;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate  ? clone $this->startDate : null;
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
     * @param User $user
     *
     * @return Day
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set absenceType
     *
     * @param AbsenceType $absenceType
     *
     * @return Day
     */
    public function setAbsenceType(AbsenceType $absenceType = null)
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
    public function setPresenceType(PresenceType $presenceType = null)
    {
        $this->presenceType = $presenceType;

        return $this;
    }

    /**
     * Get presenceType
     *
     * @return PresenceType
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

    /**
     * Set isValidated
     *
     * @param boolean $isValidated
     *
     * @return Day
     */
    public function setIsValidated($isValidated)
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * Get isValidated
     *
     * @return boolean
     */
    public function getIsValidated()
    {
        return $this->isValidated;
    }
}
