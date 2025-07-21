<?php

/**
 * Symfony FormType for Password Changes
 *
 * PHP version 8.4
 *
 * @category  Form
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

/**
 * Symfony FormType for Password Changes
 *
 * PHP version 8.4
 *
 * @category  Form
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class ChangePasswordFormType extends AbstractType
{
    /**
     * Build the Form Interface for the Controller to render
     *
     * @param FormBuilderInterface $builder FormBuilderInterface
     * @param array                $options Options for Form Builder
     *
     * @return void
     **/
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'options' => [
                        'attr' => [
                            'autocomplete' => 'new-password',
                        ],
                    ],
                    'first_options' => [
                        'constraints' => [
                            new NotBlank(
                                [
                                    'message' => 'Please enter a password',
                                ]
                            ),
                            new Length(
                                [
                                    'min' => 12,
                                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                                    // max length allowed by Symfony
                                    'max' => 4096,
                                ]
                            ),
                            new PasswordStrength(),
                            new NotCompromisedPassword(),
                        ],
                        'label' => 'New password',
                    ],
                    'second_options' => [
                        'label' => 'Repeat Password',
                    ],
                    'invalid_message' => 'The password fields must match.',
                    // Instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Reset password',
                ]
            );
    }

    /**
     * Pass the needed Data Classes to the Form Builder
     *
     * @param OptionsResolver $resolver The resolver from Class Objects to Form
     *
     * @return void
     **/
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
