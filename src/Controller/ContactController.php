<?php

/**
 * Symfony Controller for /contact Route
 *
 * PHP version 8.4
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

use App\Form\ContactType;
use App\Message\SupportRequestMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Symfony Controller for /contact Route
 *
 * PHP version 8.4
 *
 * @category  Controller
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.3
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class ContactController extends AbstractController
{
    /**
     * /app_contact Route
     *
     * @param Request             $request The http request
     * @param MessageBusInterface $bus     The message bus
     *
     * @return Response
     **/
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        MessageBusInterface $bus
    ): Response {
        $newMessage = array();

        $newMessageForm = $this->createForm(ContactType::class);
        $newMessageForm->handleRequest($request);

        $errors = $newMessageForm->getErrors(true);
        foreach ($errors as $error) {
            $this->addFlash('newMessageFormErrors', $error->getMessage());
        }

        if ($newMessageForm->isSubmitted() && $newMessageForm->isValid()) {
            $newMessage = $newMessageForm->getData();

            $bus->dispatch(new SupportRequestMessage($newMessage));

            $this->addFlash(
                'newMessageFormSuccess',
                'A new ticket has been submitted for you!'
            );

            $this->redirectToRoute('app_contact');
        }

        return $this->render(
            'contact/index.html.twig',
            [
                'new_message_form' => $newMessageForm,
            ]
        );
    }
}
