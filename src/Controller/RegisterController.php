<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $encoder;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $encoder, EntityManagerInterface $entityManagerInterface)
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManagerInterface;
    }

    #[Route('/api/register', name: 'app_register', methods : ['POST'])]
    public function index(Request $request): Response
    {
        // Get the raw JSON content from the request
        $jsonData = $request->getContent();

        // Decode the JSON content into an associative array
        $data = json_decode($jsonData, true);

        $user = new User();
        
        $user->setEmail($data['email']);
        $hash = $this->encoder->hashPassword($user, $data['password']);
        $user->setPassword($hash);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json("");
    }

    #[Route('/api/editUser/{id}', name: 'app_edit', methods : ["PUT", "GET"])]
    public function edit(Request $request, $id): Response
    {
        // Get the raw JSON content from the request
        $jsonData = $request->getContent();

        // Decode the JSON content into an associative array
        $data = json_decode($jsonData, true);

        $user = $this->entityManager->getRepository(User:: class)->find($id);

        $user->setEmail($data['email'] ?? $user->getEmail());
        $hash = $this->encoder->hashPassword($user, $data['password']);
        $user->setPassword($hash);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json("");
    }
}
