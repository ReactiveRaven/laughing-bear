<?php

namespace RRaven\Bundle\LaughingbearBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * GithubUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GithubUserRepository extends EntityRepository
{
    public function getOrgRepositories(GithubUser $user) {
        return 
            $this->
                getEntityManager()->
                createQueryBuilder()->
                select("r")->
                from("RRavenLaughingbearBundle:GithubRepository", "r")->
                innerJoin("RRavenLaughingbearBundle:GithubOrganization", "o", "WITH", "r.organization = o.id")->
                innerJoin("RRavenLaughingbearBundle:GithubUser", "u")->
                where("u.id = :userid")->
                setParameters(array("userid" => $user->getId()))->
                getQuery()->
                getResult()
        ;
    }
}
