<?php

/**
 * Symfony FormType for Contact form on the /contact route
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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Symfony FormType for Contact form on the /contact route
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
class ContactType extends AbstractType
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
                'name',
                TextType::class,
                [
                    'label' => 'Name',
                    'required' => true,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Email Address',
                    'required' => true,
                ]
            )
            ->add(
                'message',
                TextareaType::class,
                [
                    'label' => 'Message',
                    'required' => true,
                ]
            )->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Submit'
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
        $resolver->setDefaults(
            [
                'data_class' => null,
            ]
        );
    }
}
