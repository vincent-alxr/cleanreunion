<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GarbageController
{
    #[Route('/garbages', name: 'garbages')]
    public function garbages(): Response
    {
        return new Response(
            '<html><body>Garbages : </body></html>'
        );
    }
}