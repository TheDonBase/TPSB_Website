<?php
namespace App\Controller;

use App\Entity\CommandLog;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
class CommandLogController extends AbstractController
{
    #[Route('/command-log', name: 'api_command_log', methods: ['POST'])]
    public function logCommand(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $log = new CommandLog();
        $log->setDiscordUserId($data['discordUserId'] ?? 'unknown');
        $log->setUsername($data['username'] ?? 'unknown');
        $log->setCommandName($data['commandName']);
        $log->setArguments(json_encode($data['arguments'] ?? []));
        $log->setExecutedAt(new \DateTimeImmutable());

        $em->persist($log);
        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}
