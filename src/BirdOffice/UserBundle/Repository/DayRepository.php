<?php

namespace BirdOffice\UserBundle\Repository;


use BirdOffice\UserBundle\Entity\User;

class DayRepository extends \Doctrine\ORM\EntityRepository
{
    public function getList(array $args = array())
    {
        $default = array(
            'month' => null,
            'user' => null
        );

        $args = array_merge($default, $args);
        extract($args);

        $db = $this->createQueryBuilder('d');

        if (!empty($month)){
            $db->andWhere("DATE_FORMAT(d.startDate, '%m') = :month")
                ->setParameter('month', $month);
        }

        if (isset($user) && $user instanceof User){
            $db->join('d.user', 'u')
                ->addSelect('u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', $user->getId());
        }

        return $db->getQuery()->getResult();

    }


}