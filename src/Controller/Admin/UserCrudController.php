<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted('ROLE_RESTAURATEUR')) {
            $qb->andWhere('entity.id = :user');
            $qb->setParameter('user', $this->getUser()->getId());

        }

        return $qb;
    }

    public function configureActions(Actions $actions): Actions 
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::INDEX)
            ->disable(Action::NEW, Action::DELETE, Action::EDIT);
    }

    
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            //->showEntityActionsAsDropdown()
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users')
            ->setDefaultSort(['createdAt' => 'DESC']);
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {

        if($pageName === Crud::PAGE_DETAIL){
            $cart = ArrayField::new('cart', 'Cart');
        } else {
            $cart = AssociationField::new('cart', 'Cart');
        }

        return [
            IdField::new('id')->hideOnForm()->setPermission('ROLE_ADMIN'),
            ImageField::new('picture')->setBasePath('/uploads/pictures/users/')->hideOnForm(),
            TextField::new('firstname'),
            TextField::new('lastname'),
            TextField::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            TextField::new('address'),
            EmailField::new('email'),
            TextField::new('slug')->onlyOnDetail(),
            ArrayField::new('roles')->hideOnForm()->setPermission('ROLE_ADMIN'),
            DateField::new('birthday'),
            DateTimeField::new('lastConnection')->onlyOnDetail(),
            $cart->hideOnForm()->setPermission('ROLE_ADMIN'),
            DateTimeField::new('createdAt')->hideOnForm()->setPermission('ROLE_ADMIN'),
            DateTimeField::new('updatedAt')->onlyOnDetail()->setPermission('ROLE_ADMIN'),
        ];
    }
   
}
