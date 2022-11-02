<?php

namespace App\Controller;
use App\Entity\Costumes;
use App\Form\Type\CostumeType;
use App\Repository\CostumesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SiteController extends AbstractController
{
    //Route User page
    #[Route('', name: 'app_User')]
    public function User(CostumesRepository $costumesRepository): Response
    {
        $Products = $costumesRepository->findAll();

        return $this->render('site/UserPage.html.twig', [
            'products' => $Products
        ]);
    }


    // //Route Admin page
    #[IsGranted('ROLE_USER')]
    #[Route('/admin', name: 'app_Admin')]
    public function Admin(CostumesRepository $costumesRepository): Response
    {
        $Products = $costumesRepository->findAll();

        return $this->render('site/AdminPage.html.twig', [
            'products' => $Products
        ]);
    }

    //Route New product page
    #[IsGranted('ROLE_USER')]
    #[Route('/product/new', name: 'app_New')]
    public function New(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $costume = new Costumes();
        $costume->setName("");
        $costume->setPrice("");
        $costume->setDescription("");
        $costume->setImage("");

        $form = $this->createForm(CostumeType::class, $costume);
        $form= $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $costume = $form->getData(); 
            $entityManager -> persist($costume);
            $entityManager -> flush();
            return $this-> redirectToRoute('app_Admin');
        }

        return $this->renderForm('site/NewProducts.html.twig', [
            'form' => $form,
        ]);
    }


        //Route Info product page
        #[Route('/product/{id}', name: 'app_Info')]
        public function show(string $id, ManagerRegistry $doctrine, CostumesRepository $CostumesRepository): Response
        {
            
            $Costume = $doctrine->getRepository(Costumes::class)->find($id);
            $entityManager = $doctrine->getManager();
    
            $SameCostume = $entityManager->getRepository(Costumes::class)->find($id);
            $price = $SameCostume->getPrice('');
    
    
    
            // rechercher tous les produits correspondant a la marque
            $Costume_obj = $CostumesRepository->findBy(
            array('Price' => $price));
            return $this->render('site/InfoProduct.html.twig', [
                    "Costume" => $Costume, 
                    "Costume_obj" => $Costume_obj,
            ]);
        }

    //Route Delete product page
    #[IsGranted('ROLE_USER')]
    #[Route('/product/{id}/delete', name: 'app_Delete')]
    public function delete(ManagerRegistry $doctrine, Costumes $costumes, string $id): Response {
        $entityManager = $doctrine->getManager();
        if (!$costumes) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        $entityManager->remove($costumes);
        $entityManager->flush();
        return $this->redirectToRoute('app_Admin');
    }


    //Route Update product page
    #[IsGranted('ROLE_USER')]
    #[Route('/product/{id}/Update', name: 'app_Update')]
    public function update(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $CostumeUpdate = $entityManager->getRepository(Costumes::class)->find($id);
        if (!$CostumeUpdate) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        };
        $form = $this->createForm(CostumeType::class, $CostumeUpdate);
        $form= $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $CostumeUpdate = $form->getData();
            $entityManager -> persist($CostumeUpdate);
            $entityManager -> flush();

            return $this-> redirectToRoute('app_Admin',[
                'id' => $CostumeUpdate->getId(),
                
            ]);
        }

        return $this->renderForm('site/UpdateProduct.html.twig', [
            'form' => $form,]);
    }
}
