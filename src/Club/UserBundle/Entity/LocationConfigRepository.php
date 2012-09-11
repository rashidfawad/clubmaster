<?php

namespace Club\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * LocationConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LocationConfigRepository extends EntityRepository
{
  public function getByKey($key, \Club\UserBundle\Entity\Location $location = null, $fallback = true)
  {
    if ($location == null)
      $location = $this->_em->find('ClubUserBundle:Location',1);

    if (!$location) return;

    $config = $this->_em->createQueryBuilder()
      ->select('lc')
      ->from('ClubUserBundle:LocationConfig','lc')
      ->where('lc.location = :location')
      ->andWhere('lc.config = :key')
      ->setParameter('key',$key)
      ->setParameter('location',$location->getId())
      ->getQuery()
      ->getOneOrNullResult();

    if ($config)

      return $config;

    if (!count($config) && !$fallback)

      return false;

    if (!count($config)) {
      $config = $this->getByKey($key, $location->getLocation(), $fallback);

      if (count($config)) {
        return $config;
      } else {
        $config = $this->getByKey($key, $location->getLocation(), $fallback);

        if (count($config)) {
          return $config;
        } else {
          // TODO, find a way to make an infinitive loop
          throw new \Exception('Too many parents');
        }

      }
    }
  }

  public function getObjectByKey($key, \Club\UserBundle\Entity\Location $location = null, $fallback = true)
  {
    $config = $this->getByKey($key, $location, $fallback);

    if (!$config)

      return;

    switch ($config->getConfig()) {
    case 'default_currency':
      $res = $this->_em->find('ClubUserBundle:Currency',$config->getValue());
      break;
    default:
      $res = $config->getValue();
      break;
    }

    return $res;
  }

  public function addConfig($key, \Club\UserBundle\Entity\Location $location, $value)
  {
    $config = new \Club\UserBundle\Entity\LocationConfig();
    $config->setLocation($location);
    $config->setConfig($key);
    $config->setValue($value);

    return $config;
  }
}
