<?php

/**
 * Symfony FormType for PDF Uploads
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

use App\Entity\PdfTypes;
use App\Entity\PdfUploads;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * Symfony FormType for PDF Uploads
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
class PdfUploadType extends AbstractType
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
        $mimeTypeErrorMsg = 'This is not a valid PDF file!';

        $builder
            ->add(
                'pdfType',
                EntityType::class,
                [
                    'class' => PdfTypes::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'label' => 'What type of PDF are you uploading?',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Convert',
                ]
            )
            ->add(
                'fileAttachment',
                FileType::class,
                [
                    'label' => 'Upload your PDF here',
                    'mapped' => false,
                    'required' => true,
                    'constraints' => (
                        new File(
                            [
                                'maxSize' => '4096k',
                                'mimeTypes' => (
                                    [
                                        'application/pdf',
                                    ]
                                ),
                                'mimeTypesMessage' => $mimeTypeErrorMsg
                            ]
                        )
                    )
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
                'data_class' => PdfUploads::class,
            ]
        );
    }
}
