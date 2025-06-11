<?php

namespace App\Controller\API;

use App\Entity\Personnage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PersonnageController extends AbstractController
{
    #[Route('/api/create', name: 'api_create_personnage', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $name = $request->request->get('name');
        $strength = $request->request->get('strength');
        $speed = $request->request->get('speed');
        $durability = $request->request->get('durability');
        $power = $request->request->get('power');
        $combat = $request->request->get('combat');

        if (!$name) {
            return new JsonResponse(['error' => 'Missing name parameter'], 400);
        }

        $personnage = new Personnage();
        $personnage->setName($name);
        $personnage->setStrength((int)$strength);
        $personnage->setSpeed((int)$speed);
        $personnage->setDurability((int)$durability);
        $personnage->setPower((int)$power);
        $personnage->setCombat((int)$combat);
        $personnage->setHealth(100);
        $personnage->setLevel(1);

        // Gestion de l'image
        $imageFile = $request->files->get('image');
        dd(vars: $imageFile);
        if ($imageFile) {
            $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/characters';

            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0777, true);
            }

            try {
                $imageFile->move($uploadsDir, $newFilename);
                $personnage->setImagePath('/uploads/characters/' . $newFilename);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Erreur upload image: ' . $e->getMessage()], 400);
            }
        } else {
            $personnage->setImagePath('https://picsum.photos/900/600');
        }

        $em->persist($personnage);
        $em->flush();

        return new JsonResponse([
            'id' => $personnage->getId(),
            'name' => $personnage->getName(),
            'health' => $personnage->getHealth(),
            'level' => $personnage->getLevel(),
            'imagePath' => $personnage->getImagePath(),
            'strength' => $personnage->getStrength(),
            'speed' => $personnage->getSpeed(),
            'durability' => $personnage->getDurability(),
            'power' => $personnage->getPower(),
            'combat' => $personnage->getCombat(),
        ], 201);
    }

    #[Route('/api/get', name: 'api_get_all_personnages', methods: ['GET'])]
    public function getAll(EntityManagerInterface $em): JsonResponse
    {
        $personnages = $em->getRepository(Personnage::class)->findAll();

        $data = array_map(function (Personnage $p) {
            return [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'health' => $p->getHealth(),
                'level' => $p->getLevel(),
                'imagePath' => $p->getImagePath(),
                'strength' => $p->getStrength(),
                'speed' => $p->getSpeed(),
                'durability' => $p->getDurability(),
                'power' => $p->getPower(),
                'combat' => $p->getCombat(),
            ];
        }, $personnages);

        return new JsonResponse($data);
    }

#[Route('/api/edit/{id}', name: 'api_update_personnage', methods: ['POST'])]
public function update(Personnage $personnage, Request $request, EntityManagerInterface $em): JsonResponse
{
    if (!$personnage) {
        return new JsonResponse(['error' => 'Personnage not found'], 404);
    }

    $name = $request->request->get('name');
    $strength = $request->request->get('strength');
    $speed = $request->request->get('speed');
    $durability = $request->request->get('durability');
    $power = $request->request->get('power');
    $combat = $request->request->get('combat');

    if ($name) $personnage->setName($name);
    if ($strength !== null) $personnage->setHealth((int)$strength);
    if ($speed !== null) $personnage->setLevel((int)$speed);
    if ($strength !== null) $personnage->setStrength((int)$strength);
    if ($speed !== null) $personnage->setSpeed((int)$speed);
    if ($durability !== null) $personnage->setDurability((int)$durability);
    if ($power !== null) $personnage->setPower((int)$power);
    if ($combat !== null) $personnage->setCombat((int)$combat);

    

    $imageFile = $request->files->get('image');
    dd($imageFile);

    if ($imageFile) {
        $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/characters';

        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        try {
            $imageFile->move($uploadsDir, $newFilename);
            $personnage->setImagePath('/uploads/characters/' . $newFilename);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur upload image: ' . $e->getMessage()], 400);
        }
    } else {
        $personnage->setImagePath('https://picsum.photos/900/600');
    }

    $em->flush();

    return new JsonResponse([
        'message' => 'Personnage updated successfully',
        'personnage' => [
            'id' => $personnage->getId(),
            'name' => $personnage->getName(),
            'health' => $personnage->getHealth(),
            'level' => $personnage->getLevel(),
            'imagePath' => $personnage->getImagePath(),
            'strength' => $personnage->getStrength(),
            'speed' => $personnage->getSpeed(),
            'durability' => $personnage->getDurability(),
            'power' => $personnage->getPower(),
            'combat' => $personnage->getCombat(),
        ]
    ]);
}


    #[Route('/api/{id}', name: 'api_delete_personnage', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $personnage = $em->getRepository(Personnage::class)->find($id);

        if (!$personnage) {
            return new JsonResponse(['error' => 'Personnage not found'], 404);
        }

        $em->remove($personnage);
        $em->flush();

        return new JsonResponse(['message' => 'Personnage deleted successfully']);
    }
}
