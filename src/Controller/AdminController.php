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
use App\Form\ResetPasswordType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

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
    use ResetPasswordControllerTrait;

    private EntityManagerInterface $entityManager;
    private ResetPasswordHelperInterface $resetPasswordHelper;

    /**
     * ResetPasswordController constructor
     *
     * @param EntityManagerInterface       $entityManager       Entity Manager helper
     * @param ResetPasswordHelperInterface $resetPasswordHelper Reset Password
     **/
    public function __construct(
        EntityManagerInterface $entityManager,
        ResetPasswordHelperInterface $resetPasswordHelper
    ) {
        $this->entityManager = $entityManager;
        $this->resetPasswordHelper = $resetPasswordHelper;
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
     * Generate a password reset token for a specific User entity
     *
     * @param User $user The user to password reset
     *
     * @return ResetPasswordToken
     **/
    private function generatePasswordResetToken(User $user): ResetPasswordToken
    {
        return $this->resetPasswordHelper->generateResetToken($user);
    }

    /**
     * /app_admin Route
     *
     * @param Request                     $request        The http request
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     * @param MailerInterface             $mailer         The mailer interface
     *
     * @return Response
     **/
    #[Route('/admin', name: 'app_admin')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
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

                $resetToken = $this->generatePasswordResetToken($newUser);
                $welcomeToken = $resetToken->getToken();
                $expirationKey = $resetToken->getExpirationMessageKey();
                $expirationMessage = $resetToken->getExpirationMessageData();

                // This has to be done LAST
                // No other things can be done with the token after this
                $this->setTokenObjectInSession($resetToken);

                $sendEmail = (new TemplatedEmail())
                    ->from(
                        new Address(
                            'alerts@projecttiy.com',
                            'PDF2CSV Notifications'
                        )
                    )
                    ->to((string) $email)
                    ->subject('Welcome to PDF to CSV!')
                    ->htmlTemplate('admin/welcome_email.html.twig')
                    ->context(
                        [
                            'username' => $newUser->getEmail(),
                            'welcome_token' => $welcomeToken,
                            'expiration_key' => $expirationKey,
                            'expiration_message' => $expirationMessage
                        ]
                    );
                $mailer->send($sendEmail);

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

        // Start reset-password Tab
        $resetUser = new User();

        $resetUserForm = $this->createForm(
            ResetPasswordType::class,
            $resetUser
        );
        $resetUserForm->handleRequest($request);

        $errors = $resetUserForm->getErrors(true);
        foreach ($errors as $error) {
            $this->addFlash('resetFormErrors', $error->getMessage());
        }

        if ($resetUserForm->isSubmitted() && $resetUserForm->isValid()) {
            /**
             * This is the full App\Entity\User object
             *
             * @var User $resetUser
             **/
            $resetUser = $resetUserForm->get('email')->getData();
            try {
                $resetToken = $this->generatePasswordResetToken($resetUser);
                $token = $resetToken->getToken();
                $this->setTokenObjectInSession($resetToken);

                $this->addFlash('reset_token', $token);
                $msg = 'Reset password token successfully generated! ';
                $msg .= 'Please click on the Reset Password? tab to ';
                $msg .= 'retrieve the URL!';
                $this->addFlash('resetFormSuccess', $msg);
            } catch (TooManyPasswordRequestsException $e) {
                $msg = 'User already has an active password reset request!';
                $this->addFlash('resetFormErrors', $msg);
            }

            return $this->redirectToRoute('app_admin');
        }
        // End reset-password Tab

        return $this->render(
            'admin/index.html.twig',
            [
                'create_user_form' => $createUserForm,
                'delete_user_form' => $deleteUserForm,
                'reset_user_form' => $resetUserForm,
            ]
        );
    }
}
