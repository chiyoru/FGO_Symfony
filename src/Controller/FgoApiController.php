<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Form\Type\ClassesType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\HttpFoundation\Request;

class FgoApiController extends AbstractController
{
    public function index(HttpClientInterface $client, Request $request): Response
    {
        return $this->render(
            'fgo_api/index.html.twig'
        );
    }

    public function servantList(HttpClientInterface $client, Request $request): Response
    {
        $session = $request->getSession(); //Create session
        $session->remove('classe'); //Remove it on load of page

        $routeParameters = $request->attributes->get('_route_params'); //get route parameters
        $classe = $routeParameters['classe']; //get classe name

        $session->set('classe', $classe); //Put it in a session for a later use

        $response = $client->request( //Api
            'GET',
            'https://api.atlasacademy.io/nice/NA/servant/search?className=' . $classe
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        $result = $content;
        return $this->render(
            'fgo_api/servants.html.twig',
            [
                'servant' => $result,
                'nb_servant' => count($result) - 1,
                'classe' => $classe
            ]
        );
    }

    public function servant(HttpClientInterface $client, Request $request): Response
    {

        $routeParameters = $request->attributes->get('_route_params');
        $idServant = $routeParameters['id'];
        $session = $request->getSession();

        $classe = $session->get('classe');
        $response = $client->request(
            'GET',
            'https://api.atlasacademy.io/nice/NA/servant/search?className=' . $classe
        );

        $statusCode = $response->getStatusCode();
        // $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        //Get the right character
        if ($idServant == 'BB_(Summer)') { //if servant is BB summer, also search by rarity because they share the same name...
            foreach ($content as $servants => $key) {
                if ($key['name'] != 'BB') {
                    unset($content[$servants]);
                }
                if ($key['rarity'] != 5) {
                    unset($content[$servants]);
                }
            }
        } else  if ($idServant == 'BB') {
            foreach ($content as $servants => $key) {
                if ($key['name'] != 'BB') {
                    unset($content[$servants]);
                }
                if ($key['rarity'] != 4) {
                    unset($content[$servants]);
                }
            }
        } else {
            foreach ($content as $servants => $key) { //else, search by name only, but replace some characters by others. It was needed since URL don't allow certain characters
                if ($key['name'] != str_replace(array('_', '&'), array(' ', '/'), $idServant)) {
                    unset($content[$servants]);
                }
            }
        }

        $result = array_merge(...$content); //merge content, to remove indexation

        $skill1 = array();
        $skill2 = array();
        $skill3 = array();

        $np = array();

        //noble phantasms details, for each that exists (it includes card type changes (like S.Ishtar or Emiya) and np upgrades)
        foreach ($result['noblePhantasms'] as $noblePhantasm) {
            $np['name'][] = $noblePhantasm['name']; 
            $detail = preg_replace_callback( //after "&", capitalize first letter - used to make a line break
                '/\&\s*\K\w/',
                function ($m) {
                    return strtoupper($m[0]);
                },
                $noblePhantasm['detail']
            );
            $np['detail'][] = $detail;
            $np['card'][] = ucfirst($noblePhantasm['card']);
            $np['rank'][] = $noblePhantasm['rank'];
            $np['type'][] = $noblePhantasm['type'];

            if (array_key_exists('Value', $noblePhantasm['functions'][0]['svals'][0]) && $noblePhantasm['functions'][0]['svals'][0]['Value'] > 1000) {
                $np['npValue'][] = $noblePhantasm['functions'][0]['svals'];
            } else {
                $np['npValue'][] = $noblePhantasm['functions'][1]['svals'];
            }
        }

        //skills details
        foreach ($result['skills'] as $skill) {
            switch ($skill['num']) {
                case 1:
                    $skill1['name'][] = $skill['name'];
                    $detail = preg_replace_callback(
                        '/\&\s*\K\w/',
                        function ($m) {
                            return strtoupper($m[0]);
                        },
                        $skill['detail']
                    );
                    $skill1['detail'][] = $detail;
                    $skill1['icon'][] = $skill['icon'];
                    break;
                case 2:
                    $skill2['name'][] = $skill['name'];
                    $detail = preg_replace_callback(
                        '/\&\s*\K\w/',
                        function ($m) {
                            return strtoupper($m[0]);
                        },
                        $skill['detail']
                    );
                    $skill2['detail'][] = $detail;
                    $skill2['icon'][] = $skill['icon'];
                    break;
                case 3:
                    $skill3['name'][] = $skill['name'];
                    $detail = preg_replace_callback(
                        '/\&\s*\K\w/',
                        function ($m) {
                            return strtoupper($m[0]);
                        },
                        $skill['detail']
                    );
                    $skill3['detail'][] = $detail;
                    $skill3['icon'][] = $skill['icon'];
                    break;
            }
        }

        return $this->render(
            'fgo_api/servant.html.twig',
            [
                'servant' => $result,
                'skill1' => $skill1,
                'skill2' => $skill2,
                'skill3' => $skill3,
                'np' => $np,
                'debug' => true

            ]
        );
    }
}

// array(3) { 
//     ["name"]=> array(2) { [0]=> string(10) "Migraine B" [1]=> string(19) "Chalice of Wealth B" } 
//     ["detail"]=> array(2) { [0]=> string(64) "Increase Mental Debuff Resist (3 turns). Restore HP for yourself" [1]=> string(174) "Increase NP Gauge ▲. Increase Arts Card effectiveness when on a [Near Water] or [City] field (3 turns) ▲. Increase Mental Debuff Resist (3 turns). Recover HP for yourself" } 
//     ["icon"]=> array(2) { [0]=> string(60) "https://static.atlasacademy.io/NA/SkillIcons/skill_00401.png" [1]=> string(60) "https://static.atlasacademy.io/NA/SkillIcons/skill_00601.png" } 
// }