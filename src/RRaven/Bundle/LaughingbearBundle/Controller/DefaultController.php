<?php

namespace RRaven\Bundle\LaughingbearBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use RRaven\Bundle\LaughingbearBundle\Entity;
use RRaven\Bundle\LaughingbearBundle\Annotations\Menu;

class DefaultController extends LaughingbearController
{

    /**
     * @Route("/hello/{name}")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function helloAction($name)
    {
        return array('name' => $name);
    }
    
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $name = null;
        
        if ($this->getUser()) {
            $name = $this->getUser()->getUsername();
        }
        
        return array('name' => $name);
    }
    
    /**
     * @Route("/buzztest")
     * @Template()
     * @Secure(roles="ROLE_USER")
     * @Menu\Item(name="Buzz Test", path={"Debug"})
     */
    public function buzzTestAction()
    {
      $menumenu = $this->get("rraven.helper.menumenu");
      /* @var $menumenu \RRaven\Bundle\LaughingbearBundle\Helper\MenuMenuHelper */
      $menumenu->sniffMenus();
      
        $em = $this->getDoctrine()->getEntityManager();
        
        $githubUser_repo = $em->getRepository("RRavenLaughingbearBundle:GithubUser");
        $githubUser = $githubUser_repo->findOneBy(array("login" => $this->getUser()->getUsername()));
        /* @var $githubUser Entity\GithubUser */
        $accessToken = $githubUser->getAccessToken();
        /* @var $accessToken Entity\GithubAccessToken */
        $response = $accessToken->makeGetRequest("user/orgs");
        
        return array("json" => $this->prettifyJson($response->getContent()));
    }
    
    /**
     * @Route("/repos")
     * @Template()
     * @Secure(roles="ROLE_USER")
     * @Menu\Item(name="List Repos", path={"Debug", "Github"})
     */
    public function listReposAction() {
      $browser = $this->getBuzzBrowser();
      
      $myorgs = json_decode(
        $browser->get(
          "https://api.github.com/user/orgs",
          array("Authorization: token " . $this->getAccessToken())
        )->getContent(),
        true
      );
      
      $repo_urls = array("https://api.github.com/user/repos");
      
      foreach ($myorgs as $org) {
        $repo_urls[] = $org["repos_url"];
      }
      
      $repos = array();
      
      foreach ($repo_urls as $repo_url) {
        $somerepos = json_decode(
          $browser->get(
            $repo_url,
            array("Authorization: token " . $this->getAccessToken())
          )->getContent(), 
          true
        );
        
        foreach ($somerepos as $arepo) {
          if ($arepo["permissions"]["push"] === true) {
            $repos[$arepo["full_name"]] = $arepo;
          }
        }
      }
      
      $em = $this->getDoctrine()->getEntityManager();
      
      $repo_repository = $em->getRepository("RRavenLaughingbearBundle:GithubRepository");
      $user_repository = $em->getRepository("RRavenLaughingbearBundle:GithubUser");
      $user_object = $user_repository->findOneBy(array("login" => $this->getUser()->getUsername()));
      
      ;
      
      foreach ($repos as $repo_data) {
        $repo_object = new Entity\GithubRepository();
        try
        {
          $result = $repo_repository->findOneBy(array("full_name" => $repo_data["full_name"]));
          if (!$result) {
            $this->getApiMapHelper()->applyDataToEntity($repo_data, $repo_object);
            $repo_object->setUser($user_object);
            $em->persist($repo_object);
          }
        }
        catch (Exception $e) {
          $e = $e; // don't care, just ignore it.
        }
      }
      $em->flush();
      
      
      return array("json"=>$this->prettifyJson($repos));
      
    }

    /**
     * @Route("/test")
     * @Template()
     * @Secure(roles="ROLE_USER")
     * @Menu\Item(name="Test", path={"Debug", "Github"}, before="@rraven_laughingbear_default_listrepos")
     * @Menu\Menu(name="Github", path={"Debug"}, before="@rraven_laughingbear_default_buzztest")
     * @Menu\Menu(name="Empty", path={"Debug"})
     */
    public function testAction()
    {
        $browser = $this->getBuzzBrowser();

        $browser->get("https://api.github.com/users/" . $this->getUser()->getUsername() . "/events", array("Authorization: token " . $this->getAccessToken()));
        $json_object = json_decode($browser->getLastResponse()->getContent(), true);

        foreach ($json_object as $index => $event) {
            if ($event["type"] !== "PushEvent") {
                unset($json_object[$index]);
            }
        }

        $commits = array();

        foreach ($json_object as $index => $pushEvent) {
            foreach ($pushEvent["payload"]["commits"] as $commit) {
                $reponame = $pushEvent["repo"]["name"];

                if (!isset($commits[$reponame])) {
                    $commits[$reponame] = array();
                }
                $commits[$reponame][$commit["sha"]] = $commit["message"];
            }
        }

        $results = array();

        foreach ($commits as $repo => $commits) {
            foreach ($commits as $sha => $message) {
                $browser->get("https://api.github.com/repos/" . $repo . "/git/commits/" . $sha, array("Authorization: token " . $this->getAccessToken()));
                $response_object = json_decode($browser->getLastResponse()->getContent(), true);
                $results[strtotime($response_object["committer"]["date"])] = array(
                    "repo" => $repo,
                    "sha" => $sha,
                    "message" => $message
                );
            }
        }

        ksort($results, SORT_NUMERIC);

        $nineam = strtotime("9AM GMT");
        $lastStart = $nineam;
        foreach (array_keys($results) as $key) {
            if ($key < $nineam) {
                unset($results[$key]);
            } else {
                $offset = $key - $lastStart;
                $time = array();
                $time["minutes"] = round($offset / 60);
                $timeformat = "D d H:i";
                $time["start"] = date($timeformat, $lastStart);
                $time["end"] = date($timeformat, $key);
                $results[$key]["time"] = $time;
                $lastStart = $key;
            }
        }

        $issues = array();

        foreach ($results as $key => $val) {
            $matches = array();
            if (preg_match("/(?P<issuenumber>#[0-9]{2,})/", $val["message"], $matches)) {
                if (!isset($issues[$val["repo"]])) {
                    $issues[$val["repo"]] = array();
                }
                if (!isset($issues[$val["repo"]][$matches["issuenumber"]])) {
                    $issues[$val["repo"]][$matches["issuenumber"]] = array();
                }
                $issues[$val["repo"]][$matches["issuenumber"]][count($issues[$val["repo"]][$matches["issuenumber"]])] = array("message" => $val["message"], "time" => $val["time"]);
            }
        }

        foreach ($issues as $repo => $ossues) {
            $grandtotal = 0;
            foreach ($ossues as $issuenum => $messages) {
                $total = 0;
                foreach ($messages as $message) {
                    $total += $message["time"]["minutes"];
                }
                $issues[$repo][$issuenum]["TOTALMINUTES"] = $total;
                $grandtotal += $total;
            }
            $issues[$repo]["TOTALMINUTES"] = $grandtotal;
        }

        return
            array(
                "response" => $this->prettifyJson(json_encode(array("results" => $results, "issues" => $issues)))
            )
        ;
    }

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * @param string $json The original JSON string to process.
     *
     * @return string Indented version of the original JSON string.
     */
    function prettifyJson($json)
    {
        if (is_array($json)) {
          $json = json_encode($json);
        }
        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = '  ';
        $newLine = "\n";
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element, 
                // output a new line and indent the next line.
            } else if (($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element, 
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

}
