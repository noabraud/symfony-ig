<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheapsharkController extends AbstractController
{
    #[Route('/', name: 'app_cheapshark')]
    public function index(): Response
    {
        $baseUrl = 'https://www.cheapshark.com/api/1.0/games?id=612';

        $curl = curl_init($baseUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new Exception("Erreur cURL: " . curl_error($curl));
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode == 200) {
            $data = json_decode($response, true);
            var_dump($data);
        } else {
            throw new Exception("Erreur HTTP $httpCode: $response");
        }

        return $this->render('cheapshark/index.html.twig', [
            'controller_name' => 'CheapsharkController',
        ]);
    }
}
