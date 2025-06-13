<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-api-key',
    description: 'Genererar en säker API-nyckel',
)]
class GenerateApiKeyCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Generera en 32 bytes slumpmässig sträng och konvertera till base64
        $apiKey = base64_encode(random_bytes(32));

        // Ta bort eventuella base64-padding tecken (=) och ersätt / och + med mer URL-vänliga tecken
        $apiKey = rtrim(strtr($apiKey, '+/', '-_'), '=');

        $io->success('Din nya API-nyckel är:');
        $io->text($apiKey);

        $io->note('Lägg till denna nyckel i din .env fil som:');
        $io->text('BOT_API_KEY=' . $apiKey);

        return Command::SUCCESS;
    }
}
