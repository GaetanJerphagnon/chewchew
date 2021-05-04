<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductCrudController extends AbstractCrudController
{
    private $productRepository;
    private $restaurantRepository;
    private $product;
    private $restaurant;

    public function __construct(RestaurantRepository $restaurantRepository, ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->restaurantRepository = $restaurantRepository;
        if(isset($_REQUEST['restaurantId']) && $restaurant = $this->restaurantRepository->find($_REQUEST['restaurantId'])){
            $this->restaurant = $restaurant;
        }
        if(isset($_REQUEST['entityId']) && $product = $this->productRepository->find($_REQUEST['entityId'])){
            $this->product = $product;
        }
    }

    public static function getEntityFqcn(): string
    {
        return Product::class;
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
        $actions->remove(Crud::PAGE_INDEX, Action::NEW)
                ->add(Crud::PAGE_INDEX, Action::DETAIL);
        if($this->isGranted('ROLE_ADMIN')){
            $actions->disable(Action::DELETE, Action::EDIT, Action::NEW);
        } else if($this->restaurant){
            $newRestaurantProduct = Action::new('newRestaurantProduct', 'Add new Product for '.$this->restaurant->getName(), 'fa fa-plus')
                    ->addCssClass("btn btn-success")
                    ->createAsGlobalAction()   
                    ->linkToCrudAction('newRestaurantProduct');
    
            $actions->add(Crud::PAGE_INDEX, $newRestaurantProduct);

            $editRestaurantProduct = Action::new('editRestaurantProduct', 'Edit')
                    ->linkToCrudAction('editRestaurantProduct');
    
            $actions->add(Crud::PAGE_INDEX, $editRestaurantProduct)->remove(Crud::PAGE_INDEX, Action::EDIT);

            $showRestaurantProduct = Action::new('showRestaurantProduct', 'Show')
            ->linkToCrudAction('showRestaurantProduct');

            $actions->add(Crud::PAGE_INDEX, $showRestaurantProduct)->remove(Crud::PAGE_INDEX, Action::DETAIL);
        }
           
        return $actions;
    }

    public function showRestaurantProduct(AdminContext $context)
    {
        $entity = $context->getEntity()->getInstance();
        // Absolutely not the proper way to do it, but found only this way
        $ref = $_GET['referrer'];
        foreach(explode('&', $ref) as $item){
            
            $param = explode('=', $item);
            if($param[0] === 'restaurantId'){
                $restaurantId = $param[1];
            }
        }

        $restaurant = $this->restaurantRepository->find($restaurantId);

        $this->denyAccessUnlessGranted('RESTAURANT_EDIT', $restaurant);
      
        return $this->redirectToRoute('profile', [
            'crudAction' => Action::DETAIL,
            'crudControllerFqcn' => ProductCrudController::class,
            'entityId' => $entity->getId(),
            'restaurantId' => $restaurantId,
        ]);
    }

    public function editRestaurantProduct(AdminContext $context)
    {
        $entity = $context->getEntity()->getInstance();
        // Absolutely not the proper way to do it, but found only this way
        $ref = $_GET['referrer'];
        foreach(explode('&', $ref) as $item){
            
            $param = explode('=', $item);
            if($param[0] === 'restaurantId'){
                $restaurantId = $param[1];
            }
        }

        $restaurant = $this->restaurantRepository->find($restaurantId);

        $this->denyAccessUnlessGranted('RESTAURANT_EDIT', $restaurant);
      
        return $this->redirectToRoute('profile', [
            'crudAction' => Action::EDIT,
            'crudControllerFqcn' => ProductCrudController::class,
            'entityId' => $entity->getId(),
            'restaurantId' => $restaurantId,
        ]);
    }

    public function newRestaurantProduct()
    {
        // Absolutely not the proper way to do it, but found only this way
        $ref = $_GET['referrer'];
        foreach(explode('&', $ref) as $item){
            
            $param = explode('=', $item);
            if($param[0] === 'restaurantId'){
                $restaurantId = $param[1];
            }
        }

        $restaurant = $this->restaurantRepository->find($restaurantId);

        $this->denyAccessUnlessGranted('RESTAURANT_CREATE', $restaurant);
      
        return $this->redirectToRoute('profile', [
            'crudAction' => Action::NEW,
            'crudControllerFqcn' => ProductCrudController::class,
            'restaurantId' => $restaurantId,
        ]);
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInSingular('Product')
            ->setEntityLabelInPlural('Products')
            /* ->setSearchFields(['categories', 'products', 'name'])*/
            ->setDefaultSort(['createdAt' => 'DESC']);

        if($this->restaurant){
            $crud->setEntityLabelInSingular('Product of '. $this->restaurant->getName())
                ->setEntityLabelInPlural('Products of '. $this->restaurant->getName());
        }

        return $crud;
    }

    public function configureFilters(Filters $filters): Filters
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $filters->add(EntityFilter::new('restaurant'));
        }

        return $filters;
    }
    
    public function configureFields(string $pageName): iterable
    {
        if($this->isGranted('ROLE_RESTAURATEUR') && $this->restaurant){
            if($this->product){
                $this->denyAccessUnlessGranted('PRODUCT_VIEW', $this->product);
            }
            $this->denyAccessUnlessGranted('RESTAURANT_VIEW', $this->restaurant);
            
        } else {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        if($pageName === Crud::PAGE_DETAIL){
            $menus = ArrayField::new('menus', 'Menus');
        } else {
            $menus = AssociationField::new('menus', 'Menus')->onlyOndetail();
        }

        if($this->restaurant){
            $restaurant = AssociationField::new('restaurant')->onlyOnIndex()->hideOnIndex();
        } else {
            $restaurant = AssociationField::new('restaurant');
        }

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setValue("sdfsdf"),
            ImageField::new('picture')->setBasePath('/uploads/pictures/products/')->hideOnForm(),
            TextField::new('slug')->onlyOnDetail(),
            MoneyField::new('price')->setCurrency('EUR'),
            TextareaField::new('description'),
            TextField::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            $restaurant,
            $menus->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->onlyOnDetail(),
        ];
    }
}
