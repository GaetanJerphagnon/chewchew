<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class RestaurantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Restaurant::class;
    }

    public function configureActions(Actions $actions): Actions 
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Retaurant')
            ->setEntityLabelInPlural('Restaurants')
            ->setSearchFields(['categories', 'products', 'name'])
            ->setDefaultSort(['createdAt' => 'DESC']);
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('categories'))
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {

        if($pageName === Crud::PAGE_DETAIL){
            $products = ArrayField::new('products', 'Products');
            $categories = ArrayField::new('categories', 'Categories');
        } else {
            $products = AssociationField::new('products', 'Products');
            $categories = AssociationField::new('categories', 'Categories');
        }

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('slug')->onlyOnDetail(),
            TextEditorField::new('description'),
            TextField::new('address'),
            TextField::new('phone'),
            TextField::new('pictureUrl'),
            $products,
            $categories,
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
   
}
