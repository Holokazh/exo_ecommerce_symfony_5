<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'En-tête(s)')
            ->setHelp('index', 'Cette partie sert à modifier l\'en-tête sur l\'accueil de votre site.');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre de l\'en-tête'),
            TextareaField::new('content', 'Contenu de l\'en-tête'),
            TextField::new('btnTitle', 'Titre du bouton'),
            TextField::new('btnUrl', 'Url de destination du bouton'),
            ImageField::new('img', 'Image de l\'en-tête')->setBasePath('uploads/headers')
                ->setUploadDir('public/uploads/headers')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
        ];
    }
}
