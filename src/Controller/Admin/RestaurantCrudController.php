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
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RestaurantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Restaurant::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted('ROLE_RESTAURATEUR')) {
            $qb->andWhere('entity.owner = :user');
            $qb->setParameter('user', $this->getUser()->getId());
        }

        return $qb;
    }

    public function configureActions(Actions $actions): Actions 
    {
        // Default Actions
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        // Update Actions
        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                    return $action->setIcon('fa fa-plus')->setCssClass('btn btn-success btn-lg')->setLabel('Add a new Restaurant');
                });

        if($this->isGranted('ROLE_RESTAURATEUR')){

            $viewProducts = Action::new('viewOrders', 'Orders TODO', 'fa fa-money-bill-wave')
                ->addCssClass('btn btn-lg btn-info bg-light text-dark mt-1 px-5 py-1 my-1 mx-1')
                ->linkToCrudAction('viewOrders');
            
            $actions->add(Crud::PAGE_INDEX, $viewProducts);

            $viewProducts = Action::new('viewProducts', 'View Products', 'fa fa-list')
                ->addCssClass('btn btn-lg btn-primary px-5 mr-0 mt-1 py-3')
                ->linkToCrudAction('viewProducts');
            
            $actions->add(Crud::PAGE_INDEX, $viewProducts);

            $actions->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE, 'viewOrders','viewProducts'])
                    ->disable(Action::SAVE_AND_CONTINUE, Action::SAVE_AND_ADD_ANOTHER);

        } else {
            $actions->disable(Action::NEW, Action::DELETE)->remove( Crud::PAGE_INDEX, Action::EDIT)->remove( Crud::PAGE_DETAIL, Action::EDIT);
        }

        return $actions;
    }

    public function viewProducts(AdminContext $context)
    {
        $restaurant = $context->getEntity()->getInstance();

        return $this->redirectToRoute('profile', [
            'crudAction' => Action::INDEX,
            'crudControllerFqcn' => ProductCrudController::class,
            'restaurantId' => $restaurant->getId(),
        ]);
    }

    public function viewOrders(AdminContext $context)
    {
        $restaurant = $context->getEntity()->getInstance();

        return $this->redirectToRoute('profile', [
            'crudAction' => Action::INDEX,
            'crudControllerFqcn' => OrderCrudController::class,
            'restaurantId' => $restaurant->getId(),
        ]);
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInSingular('Retaurant')
        ->setEntityLabelInPlural('Restaurants')
        ->setPageTitle('index', '%entity_label_plural% listing')
        ->setDefaultSort(['createdAt' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add(EntityFilter::new('categories'));
        if ($this->isGranted('ROLE_ADMIN')) {
            $filters->add(EntityFilter::new('owner'));
        }

        return $filters;
    }
    
    public function configureFields(string $pageName): iterable
    {
        $products = AssociationField::new('products', 'Products');
        $categories = AssociationField::new('categories', 'Categories');

        if($pageName === Crud::PAGE_DETAIL){
            $products = ArrayField::new('products', 'Products');
        }

        if($pageName === Crud::PAGE_INDEX){
            $categories = ArrayField::new('categories', 'Categories');
        } 

        return [
            IdField::new('id')->hideOnForm(),
            ImageField::new('picture')->setBasePath('/uploads/pictures/restaurants/')->hideOnForm(),
            TextField::new('name'),
            AssociationField::new('owner')->hideOnForm()->setPermission('ROLE_ADMIN'),
            TextField::new('slug')->onlyOnDetail(),
            TextareaField::new('description')->onlyOnDetail(),
            TextField::new('address'),
            TelephoneField::new('phone'),
            TextField::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            $products->hideOnForm(),
            $categories->setSortable(false),
            DateTimeField::new('createdAt')->onlyOnDetail(),
            BooleanField::new('isActive')->setHelp('Determine if your restaurants can be seen by customers on the website'),
        ];
    }
   
}
