<?php

namespace RRaven\Bundle\LaughingbearBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use RRaven\Bundle\LaughingbearBundle\Entity;
use RRaven\Bundle\LaughingbearBundle\Annotations\Menu;

/**
 * @Route("/esi")
 */
class EsiController extends LaughingbearController
{

    /**
     * @Route("/hello/", name="esi_hello")
     * @Template()
     */
    public function helloAction()
    {
        return array('name' => $this->getUser()->getUsername());
    }
    
    private function getResponseWithEtag($etag, $maxAge = 0)
    {
        $response = new Response();
        $response->setEtag($etag);
        $response->setSharedMaxAge($maxAge);
        $response->setVary("Cookie");
        
        return $response;
    }
    
    private function getResponseWithLastModified($lastModified = null, $maxAge = 0) {
        $response = new Response();
        $response->setLastModified($lastModified === null ? new DateTime() : $lastModified);
        $response->setSharedMaxAge($maxAge);
        $response->setVary("Cookie");
        
        return $response;
    }

    /**
     * @Route("/user/username", name="esi_user_username")
     * @Template
     */
    public function userUsernameAction()
    {
        $user = $this->getUser();
        $username = "friend";
        if ($user) {
            $username = $user->getUsername();
        }
        
        $response = $this->getResponseWithEtag(md5($username), 600);
        
        if ($response->isNotModified($this->getRequest())) {
            return $response;
        } else {
            return $this->render("RRavenLaughingbearBundle:Esi:userUsername.html.twig", array("name" => $username), $response);
        }
        
    }
    
    /**
     * @Route("/menus/top/", name="esi_menus_top")
     * @Template
     */
    public function menusTopAction()
    {
        $user = $this->getUser();
        $username = "friend";
        if ($user) {
            $username = $user->getUsername();
        }
        $etag = md5($username);
        
        $response = $this->getResponseWithEtag($etag, 600);
        
        if ($response->isNotModified($this->getRequest())) {
            return $response;
        } else {
            //return array();
            return $this->render("RRavenLaughingbearBundle:Esi:menusTop.html.twig", array(), $response);
        }
    }
    
    /**
     * @Route("/repos/list", name="esi_repos_list")
     */
    public function reposListAction() {
        
        $lastAction = $this->actionGetLatest("listRepos");
        
        $lastModified = new \DateTime();
        
        if ($lastAction instanceof Entity\Action) {
            /* @var $lastAction Entity\Action */
            $lastModified = $lastAction->getUpdated();
        }
        
        $response = $this->getResponseWithLastModified($lastModified, 600);
        
        if ($response->isNotModified($this->getRequest())) {
            return $response;
        } else {
            
            $em = $this->getEntityManager();
            
            $accessToken = $this->getAccessTokenObject();

            $myorgs = json_decode(
              $accessToken->makeGetRequest(
                "https://api.github.com/user/orgs"
              )->getContent(), true
            );

            $repo_urls = array("https://api.github.com/user/repos");

            foreach ($myorgs as $org) {
                $repo_urls[] = $org["repos_url"];
            }

            $repos = array();

            foreach ($repo_urls as $repo_url) {
                $somerepos = json_decode(
                  $accessToken->makeGetRequest(
                    $repo_url
                  )->getContent(), true
                );

                foreach ($somerepos as $arepo) {
                    if ($arepo["permissions"]["push"] === true) {
                        $repos[$arepo["full_name"]] = $arepo;
                    }
                }
            }

            $repo_repository = $em->getRepository("RRavenLaughingbearBundle:GithubRepository");
            $github_user = $this->getGithubUser();

            foreach ($repos as $repo_data) {
                $repo_object = new Entity\GithubRepository();
                try {
                    $result = $repo_repository->findOneBy(array("full_name" => $repo_data["full_name"]));
                    if (!$result) {
                        $this->getApiMapHelper()->applyDataToEntity($repo_data, $repo_object);
                        $repo_object->setUser($github_user);
                        $em->persist($repo_object);
                    }
                } catch (\Exception $e) {
                    $e = $e; // don't care, just ignore it.
                }
            }

            $this->actionComplete("listRepos");

            $this->markNeedsFlush();

            $githubUser = $this->getGithubUser();

            $myrepos = $githubUser->getRepositories();
            $github_user_repository = $em->getRepository("RRavenLaughingbearBundle:GithubUser"); 
            /* @var $github_user_repository Entity\GithubUserRepository */
            $orgrepos = $github_user_repository->getOrgRepositories($githubUser);

            $combined_repos = array();
            foreach ($myrepos as $repo) {
                /* @var $repo Entity\GithubRepository */
                $combined_repos[strtolower($repo->getFullName())] = $repo;
            }

            foreach ($orgrepos as $repo) {
                /* @var $repo Entity\GithubRepository */
                $combined_repos[strtolower($repo->getFullName())] = $repo;
            }

            ksort($combined_repos);

            $repo_list = array();
            foreach ($combined_repos as $repo) {
                $bits = explode("/", $repo->getFullName());
                $owner = $bits[0];
                $name = $bits[1];
                if (!isset($repo_list[$owner])) {
                    $repo_list[$owner] = array();
                }
                $repo_list[$owner][$name] = $repo;
            }
            //return array();
            return $this->render("RRavenLaughingbearBundle:Esi:reposList.html.twig", array("repolist" => $repo_list), $response);
        }
    }

}
