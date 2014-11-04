<?php

namespace Ak76\SortableBehaviorBundle\Service;

use Doctrine\ORM\EntityManager;

class PositionHandler
{
    const UP        = 'up';
    const DOWN      = 'down';
    const TOP       = 'top';
    const BOTTOM    = 'bottom';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $object
     * @param $move
     */
    public function updatePosition(&$object, $move)
    {
//        $property = $this->getPropertyName($object);

        $last_position = $this->getLastPosition(get_class($object));
        $new_position = $this->getNewPosition($object, $move, $last_position);
        $object->setPosition($new_position);
    }

    /**
     * @param $object
     * @param $move
     * @param $last_position
     * @return int
     */
    protected function getNewPosition($object, $move, $last_position)
    {
        switch ($move) {
            case self::UP:
                if ($object->getPosition() > 0) {
                    $new_position = $object->getPosition() - 1;
                }
                break;

            case self::DOWN:
                if ($object->getPosition() < $last_position) {
                    $new_position = $object->getPosition() + 1;
                }
                break;

            case self::TOP:
                if ($object->getPosition() > 0) {
                    $new_position = 0;
                }
                break;

            case self::BOTTOM:
                if ($object->getPosition() < $last_position) {
                    $new_position = $last_position;
                }
                break;
        }

        $this->updateAll($object, $move);

        return $new_position;
    }

    /**
     * @param $entity
     * @return int
     */
    public function getLastPosition($entity)
    {
        $query = $this->em->createQuery('SELECT MAX(m.position) FROM ' . $entity . ' m');
        $result = $query->getResult();

        if (array_key_exists(0, $result)) {
            return intval($result[0][1]);
        }

        return 0;
    }

    /**
     * Reorder all records except moved record
     * @param $object
     * @param $move
     */
    protected function updateAll($object, $move)
    {
        $query = $this->em->createQueryBuilder();
        $query ->update(get_class($object), 'p');

        switch ($move) {
            case self::UP:
                $query->set('p.position', 'p.position + 1')
                    ->where('p.position = '.($object->getPosition() - 1));
                break;

            case self::DOWN:
                $query->set('p.position', 'p.position - 1')
                    ->where('p.position = '.($object->getPosition() + 1));
                break;

            case self::TOP:
                $query->set('p.position', 'p.position + 1')
                    ->where('p.position < '.$object->getPosition());
                break;

            case self::BOTTOM:
                $query->set('p.position', 'p.position - 1')
                    ->where('p.position > '.$object->getPosition());
                break;
        }

        $query->getQuery()->execute();
    }

    protected function getPropertyName($object)
    {
        $metadata = $this->em->getClassMetadata(get_class($object));
        return $metadata->getFieldMapping($metadata);
    }
}
