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
    public function fetchFgoDB(HttpClientInterface $client, Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('classe');

        #region form#
        $classes = new Classes();
        // $task->setClasse('Rider');

        $choices = array(
            'Saber' => 'saber',
            'Archer' => 'archer',
            'Lancer' => 'lancer',
            'Rider' => 'rider',
            'Caster' => 'caster',
            'Assassin' => 'assassin',
            'Berserker' => 'berserker',
            'Ruler' => 'ruler',
            'Avenger' => 'avenger',
            'Moon Cancer' => 'moonCancer',
            'Alter Ego' => 'alterEgo',
            'Foreigner' => 'foreigner',
            'Pretender' => 'pretender',
            'Shielder' => 'shielder',
            // 'Beast' => 'beast'
        );

        $form = $this->createForm(ClassesType::class, $classes, ['choices' => $choices]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $selectedVal = $task->getClasse();
            $session->set('classe', $selectedVal);
            return $this->redirectToRoute('servants');
        }
        #endregion form#

        return $this->render(
            'fgo_api/index.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    public function servantList(HttpClientInterface $client, Request $request): Response
    {
        $session = $request->getSession();

        $classe = $session->get('classe');
        $response = $client->request(
            'GET',
            'https://api.atlasacademy.io/export/NA/nice_servant.json'
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        $result = array_filter($content, function ($item) use ($classe) {
            if (stripos($item['className'], $classe) !== false) {
                return true;
            }
            return false;
        });

        $result = array_values($result);

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
            'https://api.atlasacademy.io/export/NA/nice_servant.json'
        );

        $statusCode = $response->getStatusCode();
        // $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        foreach ($content as $servants => $key) {
            if ($key['className'] != $classe) {
                unset($content[$servants]);
            }
        }

        $content = array_values($content);

        foreach ($content as $servants => $key) {
            if ($key['name'] != str_replace('_', ' ', $idServant)) {
                unset($content[$servants]);
            }
        }

        
        $result = array_merge(...$content);
        return $this->render(
            'fgo_api/servant.html.twig',
            [
                'servant' => $result,

            ]
        );
    }
}
