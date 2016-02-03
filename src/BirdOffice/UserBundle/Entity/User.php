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
 * @ORM\Table(name="bird_user")
 * @ORM\Entity(repositoryClass="BirdOffice\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $civility;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="BirdOffice\UserBundle\Entity\User")
     **/
    private $managedBy;

    /**
     * @ManyToMany(targetEntity="User", inversedBy="managedBy")
     * @JoinTable(name="managers",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="manager_user_id", referencedColumnName="id")}
     *      )
     */
    private $managers;


    public function __construct()
    {
        parent::__construct();

        $this->managedBy = new \Doctrine\Common\Collections\ArrayCollection();
        $this->managers = new \Doctrine\Common\Collections\ArrayCollection();
    }



    /**
     * Add managedBy
     *
     * @param \BirdOffice\UserBundle\Entity\User $managedBy
     *
     * @return User
     */
    public function addManagedBy(\BirdOffice\UserBundle\Entity\User $managedBy)
    {
        $this->managedBy[] = $managedBy;

        return $this;
    }

    /**
     * Remove managedBy
     *
     * @param \BirdOffice\UserBundle\Entity\User $managedBy
     */
    public function removeManagedBy(\BirdOffice\UserBundle\Entity\User $managedBy)
    {
        $this->managedBy->removeElement($managedBy);
    }

    /**
     * Get managedBy
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getManagedBy()
    {
        return $this->managedBy;
    }

    /**
     * Add manager
     *
     * @param \BirdOffice\UserBundle\Entity\User $manager
     *
     * @return User
     */
    public function addManager(\BirdOffice\UserBundle\Entity\User $manager)
    {
        $this->managers[] = $manager;

        return $this;
    }

    /**
     * Remove manager
     *
     * @param \BirdOffice\UserBundle\Entity\User $manager
     */
    public function removeManager(\BirdOffice\UserBundle\Entity\User $manager)
    {
        $this->managers->removeElement($manager);
    }

    /**
     * Get managers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * Set civility
     *
     * @param string $civility
     *
     * @return User
     */
    public function setCivility($civility)
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * Get civility
     *
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
