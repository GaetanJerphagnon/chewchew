<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\RestaurantRepository;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class OrderCrudController extends AbstractCrudController
{
    private $restaurantRepository;
    private $restaurant;

    public function __construct(RestaurantRepository $restaurantRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
        if(isset($_GET['restaurantId']) && $restaurant = $this->restaurantRepository->find($_REQUEST['restaurantId'])){
            $this->restaurant = $restaurant;
        }
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if($this->restaurant){
            $qb->andWhere('entity.restaurant = :restaurant');
            $qb->setParameter('restaurant', $this->restaurant->getId());
        }

        return $qb;
    }

    public function configureActions(Actions $actions): Actions 
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable( Action::EDIT, Action::DELETE);
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInSingular('Order')
        ->setEntityLabelInPlural('Orders')
        ->setDefaultSort(['createdAt' => 'DESC']);

        if($this->restaurant){
            $crud->setEntityLabelInSingular('Order of '. $this->restaurant->getName())
                ->setEntityLabelInPlural('Orders of '. $this->restaurant->getName());
        }

        return $crud;
    }

    
    public function configureFields(string $pageName): iterable
    {

        return [
            IdField::new('id')->hideOnForm(),
            MoneyField::new('total')->setCurrency('EUR')->hideOnForm(),
            AssociationField::new('user'),
            TextField::new('user.address', 'Delivery Address'),
            ArrayField::new('orderHasProducts', 'Products'),
            //ArrayField::new('menus', 'Menus'),
            AssociationField::new('restaurant'),
            DateTimeField::new('createdAt'),
        ];
    }
   
}
