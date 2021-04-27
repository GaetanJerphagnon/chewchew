<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MenuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }
    
    public function configureActions(Actions $actions): Actions 
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Menu')
            ->setEntityLabelInPlural('Menus')
            ->setDefaultSort(['createdAt' => 'DESC']);
        ;
    }

 /*    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('products'))
            ->add(EntityFilter::new('restaurants'))
        ;
    } */

    public function configureFields(string $pageName): iterable
    {


        if($pageName === Crud::PAGE_DETAIL){
            $products = ArrayField::new('products', 'Products');
        } else {
            $products = AssociationField::new('products', 'Products');
        }

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            MoneyField::new('price')->setCurrency('EUR'),
            TextField::new('slug')->onlyOnDetail(),
            TextareaField::new('description'),
            $products,
            DateTimeField::new('createdAt', 'Creation date')->hideOnForm()
        ];
    }
}
