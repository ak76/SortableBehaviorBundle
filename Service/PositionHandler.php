<?php

namespace Ak76\SortableBehaviorBundle\Service;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Admin;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class PositionHandler
{
    const MOVE_UP        = 'up';
    const MOVE_DOWN      = 'down';
    const MOVE_TOP       = 'top';
    const MOVE_BOTTOM    = 'bottom';

    /** @var EntityManager */
    protected $em;

    /** @var PropertyAccessor */
    protected $propertyAccessor;

    /** @var int */
    protected $lastPosition;

    /**
     * @param EntityManager $entityManager
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(EntityManager $entityManager, PropertyAccessor $propertyAccessor)
    {
        $this->em = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Get last position - max value of the $property in $entity
     *
     * @param $entity
     * @param $property
     * @param bool $forceUpdate
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastPosition($entity, $property, $forceUpdate = false)
    {
        if ($this->lastPosition == null || $forceUpdate) {
            $this->updateLastPosition($entity, $property);
        }

        return $this->lastPosition;
    }

    /**
     * Update position value
     *
     * @param $object
     * @param $property
     * @param $move
     */
    public function updatePosition(&$object, $property, $move)
    {
        $this->updateLastPosition(get_class($object), $property);
        $this->updateObjectPosition($object, $property, $move);
    }

    /**
     * Update $property in the given $object according with $move
     *
     * @param Admin $object
     * @param $property
     * @param $move
     * @return int
     */
    protected function updateObjectPosition(&$object, $property, $move)
    {
        $currentPosition = $this->propertyAccessor->getValue($object, $property);

        switch ($move) {
            case self::MOVE_UP:
                if ($currentPosition > 0) {
                    $currentPosition--;
                }
            break;

            case self::MOVE_DOWN:
                if ($currentPosition < $this->lastPosition) {
                    $currentPosition++;
                }
            break;

            case self::MOVE_TOP:
                if ($currentPosition > 0) {
                    $currentPosition = 0;
                }
            break;

            case self::MOVE_BOTTOM:
                if ($currentPosition < $this->lastPosition) {
                    $currentPosition = $this->lastPosition;
                }
            break;
        }

        $this->propertyAccessor->setValue($object, $property, $currentPosition);
    }

    /**
     * Get max value of the $property from $entity
     *
     * @param $entity
     * @param $property
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function updateLastPosition($entity, $property)
    {
        $query = $this->em->createQuery('SELECT MAX(m.' . $property . ') AS last_position FROM ' . $entity . ' m');
        $result = $query->getOneOrNullResult();
        $this->lastPosition = is_null($result['last_position']) ? 0 : $result['last_position'];
    }
}
