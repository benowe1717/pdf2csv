<?php

/**
 * Symfony Controller for Reset Password Routes
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
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * Symfony Controller for Reset Password Routes
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
#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    /**
     * ResetPasswordController constructor
     *
     * @param ResetPasswordHelperInterface $resetPasswordHelper Reset Password helper
     * @param EntityManagerInterface       $entityManager       Entity Manager helper
     **/
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     *
     * @param Request             $request    The http request
     * @param MailerInterface     $mailer     The mailer interface
     * @param TranslatorInterface $translator The translation interface
     *
     * @return Response
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(
        Request $request,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * Email submitted by user
             *
             * @var string $email
             */
            $email = $form->get('email')->getData();

            return $this->processSendingPasswordResetEmail(
                $email,
                $mailer,
                $translator
            );
        }

        return $this->render(
            'reset_password/request.html.twig',
            [
                'requestForm' => $form,
            ]
        );
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @return Response
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist
        // or someone hit this page directly.
        // This prevents exposing whether or not a user was found
        // with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render(
            'reset_password/check_email.html.twig',
            [
                'resetToken' => $resetToken,
            ]
        );
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @param Request                     $request        The http request
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     * @param TranslatorInterface         $translator     The translation interface
     * @param string                      $token          The token from the user
     *
     * @return Response
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        TranslatorInterface $translator,
        ?string $token = null
    ): Response {
        if ($token) {
            // We store the token in session and remove it from the URL,
            // to avoid the URL being
            // loaded in a browser and potentially leaking the token
            // to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException(
                'No reset password token found in the URL or in the session.'
            );
        }

        try {
            /**
             * The user to reset the password for
             *
             * @var User $user
             */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash(
                'reset_password_error',
                sprintf(
                    '%s - %s',
                    $translator->trans(
                        ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                        [],
                        'ResetPasswordBundle'
                    ),
                    $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
                )
            );

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            /**
             * The plaintext password submitted by the user
             *
             * @var string $plainPassword
             */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode(hash) the plain password, and set it.
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_resetpassword');
        }

        return $this->render(
            'reset_password/reset.html.twig',
            [
                'resetForm' => $form,
            ]
        );
    }

    /**
     * Processes the sending of reset password emails
     *
     * @param string              $emailFormData Email data from form
     * @param MailerInterface     $mailer        The mailer interface
     * @param TranslatorInterface $translator    The translation interface
     *
     * @return RedirectResponse
     */
    private function processSendingPasswordResetEmail(
        string $emailFormData,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): RedirectResponse {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            [
                'email' => $emailFormData,
            ]
        );

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent,
            // uncomment the lines below and change the redirect
            // to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash(
            //     'reset_password_error',
            //     sprintf(
            //         '%s - %s',
            //         $translator->trans(
            //             ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE,
            //             [],
            //             'ResetPasswordBundle'
            //         ),
            //         $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            //     )
            // );

            return $this->redirectToRoute('app_check_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('alerts@projecttiy.com', 'PDF2CSV Notifications'))
            ->to((string) $user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context(
                [
                    'resetToken' => $resetToken,
                ]
            );

        $mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}
