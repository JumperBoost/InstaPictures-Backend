<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DownloadController extends AbstractController {
    #[Route('/download/{url}', requirements: ['url' => '.+'])]
    public function download(Request $request, mixed $url) {
        $file = file_get_contents($url . '?' . $request->getQueryString());
        $response = new Response($file);
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($url) . '";');
        $response->sendHeaders();
        return $response;
    }
    #[Route('/download')]
    public function index(): Response {
        return new Response("Merci d'indiquer un URL.", 400);
    }
}
