<?php

/**
 * Symfony FormType for User Deletion
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

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Symfony FormType for User Deletion
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
class DeleteUserType extends AbstractType
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
                'email',
                EntityType::class,
                [
                    'class' => User::class,
                    'choices' => $options['users'],
                    'choice_label' => 'email',
                    'choice_value' => 'id',
                    'label' => 'Which user do you want to delete?',
                    'mapped' => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Delete',
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
                'data_class' => User::class,
                'users' => User::class,
            ]
        );
    }
}
