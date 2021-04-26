<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureActions(Actions $actions): Actions 
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Category')
            ->setEntityLabelInPlural('Categories')
            ->setSearchFields(['restaurants', 'products', 'name'])
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
            $restaurants = ArrayField::new('restaurants', 'Restaurants');
        } else {
            $products = AssociationField::new('products', 'Products');
            $restaurants = AssociationField::new('restaurants', 'Restaurants');
        }

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('slug')->onlyOnDetail(),
            TextareaField::new('description'),
            $restaurants->hideOnForm(),
            $products->hideOnForm(),
            DateTimeField::new('createdAt', 'Creation date')->hideOnForm()
        ];
    }
   
}
