<?php
namespace App\Controller;

use App\Entity\BotErrorLog;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
class BotErrorLogController extends AbstractController
{
    #[Route('/error-log', name: 'api_error_log', methods: ['POST'])]
    public function logError(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $error = new BotErrorLog();
        $error->setMessage($data['message']);
        $error->setLevel($data['level'] ?? 'error');
        $error->setStackTrace($data['stackTrace'] ?? null);
        $error->setTimestamp(new \DateTime());

        $em->persist($error);
        $em->flush();

        return new JsonResponse(['status' => 'logged']);
    }

}
