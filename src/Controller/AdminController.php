<?php


/**
 * Symfony Controller for /admin Route
 *
 * PHP version 8.3
 *
 * @category  Controller
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserType;
use App\Form\DeleteUserType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Symfony Controller for /admin Route
 *
 * PHP version 8.3
 *
 * @category  Controller
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * ResetPasswordController constructor
     *
     * @param EntityManagerInterface $entityManager Entity Manager helper
     **/
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Generate a cryptographically secure random password
     * for the create-user form. The password doesn't matter
     * as the user will just reset it anyways
     *
     * @return string
     **/
    private function generateRandomPassword(): string
    {
        $token = bin2hex(random_bytes(16));
        return hash("sha256", $token);
    }

    /**
     * Get all non-admin users to populate the deleteUserForm
     *
     * @return ?User
     **/
    private function getUsersToDelete(): array
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        return $userRepository->getAllUnprivilegedUsers();
    }

    /**
     * /app_admin Route
     *
     * @param Request                     $request        The http request
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     *
     * @return Response
     **/
    #[Route('/admin', name: 'app_admin')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Start create-user Tab
        $newUser = new User();

        $createUserForm = $this->createForm(
            CreateUserType::class,
            $newUser
        );
        $createUserForm->handleRequest($request);

        $errors = $createUserForm->getErrors(true);
        foreach ($errors as $error) {
            $this->addFlash('createFormErrors', $error->getMessage());
        }

        if ($createUserForm->isSubmitted() && $createUserForm->isValid()) {
            $email = $createUserForm->get('email')->getData();
            $newUser->setEmail($email);
            $newUser->setRoles(['ROLE_USER']);
            $newUser->setPassword(
                $passwordHasher->hashPassword(
                    $newUser,
                    $this->generateRandomPassword()
                )
            );

            $this->entityManager->persist($newUser);
            try {
                $this->entityManager->flush();
                $this->addFlash('createFormSuccess', 'User created successfully!');
                return $this->redirectToRoute('app_admin');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('createFormErrors', $e->getMessage());
            }
        }
        // End create-user Tab

        // Start delete-user Tab
        $deleteUser = new User();

        $deleteUserForm = $this->createForm(
            DeleteUserType::class,
            $deleteUser,
            [
                'users' => $this->getUsersToDelete(),
            ]
        );
        $deleteUserForm->handleRequest($request);

        $errors = $deleteUserForm->getErrors(true);
        foreach ($errors as $error) {
            $this->addFlash('deleteFormErrors', $error->getMessage());
        }

        if ($deleteUserForm->isSubmitted() && $deleteUserForm->isValid()) {
            /**
             * This is the full App\Entity\User object
             *
             * @var User $deleteUser
             **/
            $deleteUser = $deleteUserForm->get('email')->getData();
            $this->entityManager->remove($deleteUser);
            try {
                $this->entityManager->flush();
                $this->addFlash('deleteFormSuccess', 'User deleted successfully!');
                return $this->redirectToRoute('app_admin');
            } catch (Exception $e) {
                $this->addFlash('deleteFormErrors', $e->getMessage());
            }
        }
        // End delete-user Tab

        return $this->render(
            'admin/index.html.twig',
            [
                'create_user_form' => $createUserForm,
                'delete_user_form' => $deleteUserForm,
            ]
        );
    }
}