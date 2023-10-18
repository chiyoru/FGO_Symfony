<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UsersController extends AbstractController
{

    public function userProfile(EntityManagerInterface $entityManager, ValidatorInterface $validator, Request $request, HttpClientInterface $client): Response
    {
        $session = $request->getSession(); //Create session
        $user = $entityManager->getRepository(Users::class)->findUserByUserName($session->get('username'));

        $user = array_merge(...$user);
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for username ' . $session->get('username')
            );
        }

        //API for profile icons
        $response = $client->request( //Api
            'GET',
            'https://api.atlasacademy.io/export/NA/nice_servant.json'
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        $result = array();

        foreach ($content as $icon) {
            if (array_key_exists(0, $icon['ascensionMaterials']) && $icon['ascensionMaterials'][0]['items'][0]['item']['type'] == 'eventItem') {
                $result[] = ['1' => $icon['extraAssets']['faces']['ascension'][1], '2' =>  $icon['extraAssets']['faces']['ascension'][4]];
            } else {
                $result[] = $icon['extraAssets']['faces']['ascension'];
            }
        }

        if ($request->isXmlHttpRequest()) {
            //update profile pic
            $user2 = $entityManager->getRepository(Users::class)->find($user['id']);
            $pp = $request->getContent();
           
            $user2->setPicture($pp);
            $entityManager->flush();

            return new JsonResponse($pp);
        }

        return $this->render(
            'user/user_profile.html.twig',
            [
                'username' => $user['username'],
                'password' => $user['password'],
                'picture' => urldecode(str_replace('url=', '', $user['picture'])),
                'icons' => $result,
                'debug' => true
            ]
        );
    }
}
