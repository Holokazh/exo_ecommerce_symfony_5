<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class OrderCrudController extends AbstractCrudController
{
    public $entityManager;
    public $adminUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDeliveryInProgress = Action::new('updateDeliveryInProgress', 'En cours de livraison', 'fas fa-truck')->linkToCrudAction('updateDeliveryInProgress');
        $updateDelivery = Action::new('updateDelivery', 'Livrée', 'fas fa-home')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDeliveryInProgress)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice', '<span style="color: green"><strong>La commande ' . $order->getReference() . ' est bien en cours de préparation.</strong></span>');

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        $content = 'Bonjour' . $order->getUser()->getFirstname() . ' !<br/>Votre commande est en cours de péparation.';

        $mail = new Mail;
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullname(), 'Klevor - Commande en cours de préparation', $content);

        return $this->redirect($url);
    }

    public function updateDeliveryInProgress(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice', '<span style="color: green"><strong>La commande ' . $order->getReference() . ' est bien en cours de livraison.</strong></span>');

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        $content = 'Bonjour' . $order->getUser()->getFirstname() . ' !<br/>Votre commande est en cours de livraison.';

        $mail = new Mail;
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullname(), 'Klevor - Commande en cours de livraison', $content);

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(4);
        $this->entityManager->flush();

        $this->addFlash('notice', '<span style="color: green"><strong>La commande ' . $order->getReference() . ' a bien été livrée.</strong></span>');

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        $content = 'Bonjour' . $order->getUser()->getFirstname() . ' !<br/>Votre commande a bien été livrée.';

        $mail = new Mail;
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullname(), 'Klevor - Votre commande a bien été livrée', $content);

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle('index', 'Commande(s)');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('CreatedAt', 'Passée le'),
            TextField::new('user.getFullName', 'Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('total')->setCurrency('EUR'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state', 'statut')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'En cours de livraison' => 3,
                'Livrée' => 4,
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }
}
