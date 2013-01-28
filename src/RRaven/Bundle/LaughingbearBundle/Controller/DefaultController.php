<?php

namespace RRaven\Bundle\LaughingbearBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

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
        
        $github_user = $this->getDoctrine()->getEntityManager()->getRepository("RRavenLaughingbearBundle:GithubUser")->findOneBy(array("github_id" => $this->getUser()->getGithubId()));
        /* @var $github_user \RRaven\Bundle\LaughingbearBundle\Entity\GithubUser */
        
        $access_token = $github_user->getAccessToken();
        /* @var $access_token \RRaven\Bundle\LaughingbearBundle\Entity\GithubAccessToken */
        $access_token->container = $this->container;
        //$response = $access_token->makeGetRequest("https://api.github.com/user");
        
        $mybrowser = $this->get('buzz')->getBrowser('rraven.laughingbear.github');
        var_dump($mybrowser);
        
        //var_dump($response->getContent());
        
        
        
        return array('name' => $name);
    }

    /**
     * @Route("/test")
     * @Template()
     * @Secure(roles="ROLE_USER")
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
