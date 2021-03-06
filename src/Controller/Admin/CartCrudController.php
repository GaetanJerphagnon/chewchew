<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CartCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cart::class;
    }

    public function configureActions(Actions $actions): Actions 
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT);;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            //->showEntityActionsAsDropdown()
            ->setEntityLabelInSingular('Cart')
            ->setEntityLabelInPlural('Carts')
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {

        if($pageName === Crud::PAGE_DETAIL){
            $products = ArrayField::new('products', 'Products');
            $menus = ArrayField::new('menus', 'Menus');
        } else {
            $products = AssociationField::new('products', 'Products');
            $menus = AssociationField::new('menus', 'Menus');
        }

        return [
            IdField::new('id'),
            AssociationField::new('user'),
            $products,
            $menus,
            DateField::new('updatedAt'),
        ];
    }
   
}
