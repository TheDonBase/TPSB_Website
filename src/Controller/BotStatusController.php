<?php
namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\BotStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
class BotStatusController extends AbstractController
{
    #[Route('/bot/heartbeat', name: 'api_bot_heartbeat', methods: ['POST'])]
    public function heartbeat(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $botStatus = $entityManager->getRepository(BotStatus::class)->findOneBy([]) ?? new BotStatus();
        $botStatus->setLastSeen(new \DateTimeImmutable());

        $data = json_decode($request->getContent(), true);
        $botStatus->setPing($data['ping'] ?? null);
        $botStatus->setVersion($data['version'] ?? 'unknown');
        $botStatus->setMemoryUsage($data['memoryUsage'] ?? null);
        $botStatus->setCurrentActivity($data['currentActivity'] ?? null);

        $entityManager->persist($botStatus);
        $entityManager->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}
