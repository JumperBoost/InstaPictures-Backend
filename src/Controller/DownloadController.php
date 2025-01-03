<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DownloadController extends AbstractController {
    private static $MEDIA_TYPES = ["jpg" => "image/jpeg", "png" => "image/png", "webp" => "image/webp", "heic" => "image/heic", "mp4" => "video/mp4"];
    private static $AUTHORIZED_HOST = ["fbcdn.net", "cdninstagram.com"];

    #[Route('/download/{url}', requirements: ['url' => '.+'])]
    public function download(Request $request, mixed $url) {
        $type = static::$MEDIA_TYPES[pathinfo($url, PATHINFO_EXTENSION)] ?? null;
        if(is_null($type))
            return new Response("Le type du fichier demandé n'est pas autorisé.", 400);

        $canDownload = array_reduce(static::$AUTHORIZED_HOST, fn($carry, $host) => $carry || str_ends_with(parse_url($url)['host'], $host), false);
        if(!$canDownload)
            return new Response("Le domaine du fichier demandé n'est pas autorisé.", 400);

        // Ouvre une connexion HTTP au fichier distant
        $context = stream_context_create(['http' => ['follow_location' => true]]);
        $fp = fopen($url . '?' . $request->getQueryString(), 'rb', false, $context);
        if (!$fp)
            return new Response("Impossible de récupérer le fichier depuis l'hôte.", 500);

        header("Content-Type: $type");
        foreach ($http_response_header as $h) {
            if (str_starts_with($h, 'Content-Length:')) {
                header($h);
                break;
            }
        }

        fpassthru($fp);
        fclose($fp);

        return new Response();
    }

    #[Route('/download')]
    public function index(): Response {
        return new Response("Merci d'indiquer un URL.", 400);
    }
}
