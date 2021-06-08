<?php

namespace App\Controller\Admin;

use App\Entity\Livre;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LivreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Livre::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        // $imageFile = Field::new('imageFile')->setFormType(VichImageType::class);
        // $image = ImageField::new('image')->setBasePath('/images/livres');

        $fields = [
            IdField::new('id')->hideOnForm(),
            Field::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('image')->setBasePath('/images/livres')->hideOnForm(),
            TextField::new('isbn'),
            TextField::new('titre'),
            IntegerField::new('prix'),
            AssociationField::new('editeur'),
            IntegerField::new('annee'),
            TextField::new('langue'),
            AssociationField::new('auteur'),
            AssociationField::new('genre'),
        ];

        // if($pageName == Crud::PAGE_INDEX || $pageName == Crud::PAGE_DETAIL){
        //     $fields[] = $image;
            
        // } else {
        //     $fields[] = $imageFile;
        // }
                
        return $fields;
    }
    
}
