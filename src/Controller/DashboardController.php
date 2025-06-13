<?php

namespace App\Controller;

use App\Entity\BotErrorLog;
use App\Entity\BotStatus;
use App\Repository\BotErrorLogRepository;
use App\Repository\BotStatusRepository;
use App\Repository\CommandLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route('/home', name: 'app_dashboard')]
    public function home(BotStatusRepository $statusRepo, CommandLogRepository $cmdRepo, BotErrorLogRepository $errRepo)
    {
        $botStatus = $statusRepo->findOneBy([]);
        $isOnline = $botStatus && $botStatus->getLastSeen() > new \DateTimeImmutable('-60 seconds');
        $logs = $cmdRepo->findBy([], ['executedAt' => 'DESC'], 10);
        $errors = $errRepo->findBy([], ['timestamp' => 'DESC'], 5);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/settings', name: 'bot_settings')]
    public function settings(): Response
    {
        return $this->render('dashboard/settings.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}

