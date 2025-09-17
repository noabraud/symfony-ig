<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class LocaleController extends AbstractController
{
    #[Route('/locale', name: 'set_locale', methods: ['POST'])]
    public function setLocale(Request $request, SessionInterface $session)
    {
        $lang = $request->request->get('language', 'fr'); // récupère la langue du formulaire
        $session->set('_locale', $lang); // change la locale
        return $this->redirect($request->headers->get('referer') ?? '/');
}

}
