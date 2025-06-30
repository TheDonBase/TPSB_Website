<?php

namespace App\Controller;

use App\Entity\UserItems;
use App\Entity\Users;
use App\Entity\StatTracking;
use App\Entity\Nerve;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class ApiController extends AbstractController
{
    private function validateRequest(array $data, array $requiredFields): array
    {
        $errors = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $errors[] = "Fältet '$field' saknas";
            }
        }
        return $errors;
    }

    // UserItems endpoints
    #[Route('/user-items', name: 'api_user_items_list', methods: ['GET'])]
    public function getUserItems(EntityManagerInterface $entityManager): JsonResponse
    {
        $userItems = $entityManager->getRepository(UserItems::class)->findAll();
        return $this->json(array_map(fn($item) => [
            'id' => $item->getId(),
            'user_id' => $item->getUserId(),
            'item_id' => $item->getItemId(),
            'amount' => $item->getAmount()
        ], $userItems));
    }

    #[Route('/user-items', name: 'api_user_items_create', methods: ['POST'])]
    public function createUserItem(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validateRequest($data, ['user_id', 'item_id', 'amount']);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], 400);
        }

        if ($data['amount'] < 0) {
            return $this->json(['errors' => ['Mängden måste vara positiv']], 400);
        }

        $userItem = new UserItems();
        $userItem->setUserId($data['user_id']);
        $userItem->setItemId($data['item_id']);
        $userItem->setAmount($data['amount']);

        $entityManager->persist($userItem);
        $entityManager->flush();

        return $this->json([
            'id' => $userItem->getId(),
            'user_id' => $userItem->getUserId(),
            'item_id' => $userItem->getItemId(),
            'amount' => $userItem->getAmount()
        ], 201);
    }

    // Users endpoints
    #[Route('/users', name: 'api_users_list', methods: ['GET'])]
    public function getUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        $users = $entityManager->getRepository(Users::class)->findAll();
        return $this->json(array_map(fn($user) => [
            'id' => $user->getId(),
            'user_id' => $user->getUserId(),
            'balance' => $user->getBalance()
        ], $users));
    }

    #[Route('/users', name: 'api_users_create', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validateRequest($data, ['user_id', 'balance']);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], 400);
        }

        $user = new Users();
        $user->setUserId($data['user_id']);
        $user->setBalance($data['balance']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'id' => $user->getId(),
            'user_id' => $user->getUserId(),
            'balance' => $user->getBalance()
        ], 201);
    }

    // StatTracking endpoints
    #[Route('/stats', name: 'api_stats_list', methods: ['GET'])]
    public function getStats(EntityManagerInterface $entityManager): JsonResponse
    {
        $stats = $entityManager->getRepository(StatTracking::class)->findAll();
        return $this->json(array_map(fn($stat) => [
            'id' => $stat->getId(),
            'username' => $stat->getUsername(),
            'strength' => $stat->getStrength(),
            'speed' => $stat->getSpeed(),
            'dexterity' => $stat->getDexterity(),
            'defense' => $stat->getDefense(),
            'total' => $stat->getTotal(),
            'created_at' => $stat->getCreatedAt(),
            'updated_at' => $stat->getUpdatedAt()
        ], $stats));
    }

    #[Route('/stats/{username:username}', name: 'api_stats_update', methods: ['PATCH', 'POST'])]
    public function updateStats(
        StatTracking $username,
        Request $request,
        LoggerInterface $logger
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $logger->info('Updating stats for user ' . $username->getUsername());
        $logger->debug('Request data: ' . print_r($data, true));

        // Validera inkommande data
        $errors = [];
        $validFields = ['strength', 'speed', 'dexterity', 'defense'];
        $updatedFields = [];

        foreach ($data as $field => $value) {
            if (!in_array($field, $validFields)) {
                $errors[] = "Ogiltigt fält: $field";
                continue;
            }
            if (!is_int($value)) {
                $errors[] = "Värdet för $field måste vara ett heltal";
                continue;
            }
            if ($value < 0) {
                $errors[] = "Värdet för $field kan inte vara negativt";
                continue;
            }
            $updatedFields[$field] = $value;
        }

        if (!empty($errors)) {
            return $this->json(['errors' => $errors], 400);
        }

        // Uppdatera värdena som skickats in
        if (isset($updatedFields['strength'])) {
            $username->setStrength($updatedFields['strength']);
        }
        if (isset($updatedFields['speed'])) {
            $username->setSpeed($updatedFields['speed']);
        }
        if (isset($updatedFields['dexterity'])) {
            $username->setDexterity($updatedFields['dexterity']);
        }
        if (isset($updatedFields['defense'])) {
            $username->setDefense($updatedFields['defense']);
        }

        // Uppdatera total och tidsstämpel
        $username->setTotal(
            $username->getStrength() +
            $username->getSpeed() +
            $username->getDexterity() +
            $username->getDefense()
        );
        $username->setUpdatedAt(new \DateTimeImmutable());


        // Returnera uppdaterad data
        return $this->json([
            'id' => $username->getId(),
            'username' => $username->getUsername(),
            'strength' => $username->getStrength(),
            'speed' => $username->getSpeed(),
            'dexterity' => $username->getDexterity(),
            'defense' => $username->getDefense(),
            'total' => $username->getTotal(),
            'created_at' => $username->getCreatedAt(),
            'updated_at' => $username->getUpdatedAt()
        ]);
    }


    #[Route('/stats', name: 'api_stats_create', methods: ['POST'])]
    public function createStat(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validateRequest($data, [
            'username', 'strength', 'speed', 'dexterity', 'defense'
        ]);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], 400);
        }

        $stat = new StatTracking();
        $stat->setUsername($data['username']);
        $stat->setStrength($data['strength']);
        $stat->setSpeed($data['speed']);
        $stat->setDexterity($data['dexterity']);
        $stat->setDefense($data['defense']);
        $stat->setTotal($data['strength'] + $data['speed'] + $data['dexterity'] + $data['defense']);
        $stat->setCreatedAt(new \DateTimeImmutable());
        $stat->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($stat);
        $entityManager->flush();

        return $this->json([
            'id' => $stat->getId(),
            'username' => $stat->getUsername(),
            'strength' => $stat->getStrength(),
            'speed' => $stat->getSpeed(),
            'dexterity' => $stat->getDexterity(),
            'defense' => $stat->getDefense(),
            'total' => $stat->getTotal(),
            'created_at' => $stat->getCreatedAt(),
            'updated_at' => $stat->getUpdatedAt()
        ], 201);
    }

    // Gemensamma GET/PUT/DELETE endpoints för alla entiteter
    #[Route('/{entity}/{id}', name: 'api_get_entity', methods: ['GET'])]
    public function getEntity(string $entity, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityClass = match($entity) {
            'user-items' => UserItems::class,
            'users' => Users::class,
            'stats' => StatTracking::class,
            default => null
        };

        if (!$entityClass) {
            return $this->json(['error' => 'Ogiltig entitetstyp'], 400);
        }

        $item = $entityManager->getRepository($entityClass)->find($id);

        if (!$item) {
            return $this->json(['error' => 'Objekt hittades inte'], 404);
        }

        return $this->json($item);
    }

    #[Route('/{entity}/{id}', name: 'api_delete_entity', methods: ['DELETE'])]
    public function deleteEntity(string $entity, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityClass = match($entity) {
            'user-items' => UserItems::class,
            'users' => Users::class,
            'stats' => StatTracking::class,
            default => null
        };

        if (!$entityClass) {
            return $this->json(['error' => 'Ogiltig entitetstyp'], 400);
        }

        $item = $entityManager->getRepository($entityClass)->find($id);

        if (!$item) {
            return $this->json(['error' => 'Objekt hittades inte'], 404);
        }

        $entityManager->remove($item);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
