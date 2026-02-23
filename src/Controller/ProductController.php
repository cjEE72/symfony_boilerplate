<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\Product\Step\ProductConfirmationStepType;
use App\Form\Product\Step\ProductDetailsStepType;
use App\Form\Product\Step\ProductLicenseStepType;
use App\Form\Product\Step\ProductLogisticsStepType;
use App\Form\Product\Step\ProductTypeStepType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\CsvExporter;
use App\Service\CsvImporter;

#[Route('/product')]
class ProductController extends AbstractController
{
    // --- LISTE DES PRODUITS ---
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
       return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAllOrderedByPriceDesc(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    #[IsGranted('ADMIN_ACCESS')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $step = $request->query->getInt('step', 1);

        $product = $session->get('product_data');
        if (!$product instanceof Product) {
            $product = new Product();
            $product->setType('physique');
            $session->set('product_data', $product);
        }

        $formType = match($step) {
            1 => ProductTypeStepType::class,
            2 => ProductDetailsStepType::class,
            3 => ($product->getType() === 'physique') ? ProductLogisticsStepType::class : ProductLicenseStepType::class,
            4 => ProductConfirmationStepType::class,
            default => ProductTypeStepType::class,
        };

        $form = $this->createForm($formType, $product);
        $form->handleRequest($request);

        $shouldSave = false;
        $redirectToNext = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('product_data', $product);
            if ($step === 4 || ($step === 3 && $product->getPrice() < 500)) {
                $shouldSave = true;
            } else {
                $redirectToNext = true;
            }
        }

        if ($step === 4 && $product->getPrice() < 500) {
            $shouldSave = true;
        }

        if ($redirectToNext) {
            $response = $this->redirectToRoute('app_product_new', ['step' => $step + 1]);
        } elseif ($shouldSave) {
            $em->persist($product);
            $em->flush();
            $session->remove('product_data');
            $this->addFlash('success', 'Produit créé avec succès !');
            $response = $this->redirectToRoute('app_product_index');
        } else {
            $response = $this->render('product/step/new_step.html.twig', [
                'form' => $form->createView(),
                'step' => $step,
                'product' => $product
            ]);
        }

        return $response;
    }

    #[Route('/import', name: 'app_product_import_csv', methods: ['GET','POST'])]
    #[IsGranted('ADMIN_ACCESS')]
    public function importCsv(Request $request, CsvImporter $csvImporter, EntityManagerInterface $em, ProductRepository $productRepository): Response
    {
        if ($request->isMethod('POST')) {
            $uploadedFile = $request->files->get('csv_file');
            if ($uploadedFile) {
                $tmpPath = $uploadedFile->getRealPath();
                $result = $csvImporter->importFromPath($tmpPath, $em, $productRepository);
                $this->addFlash('success', sprintf('Import terminé : %d créés, %d mis à jour', $result['created'], $result['updated']));
                foreach ($result['errors'] as $err) {
                    $this->addFlash('error', $err);
                }
                return $this->redirectToRoute('app_product_index');
            }
            $this->addFlash('error', 'Aucun fichier reçu');
        }

        return $this->render('product/import.html.twig');
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
    
  

    // --- MODIFIER UN PRODUIT ---
    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ADMIN_ACCESS')]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Pour l'édition, on peut utiliser le ProductType standard ou le système de steps.
        // Ici on utilise le standard pour gagner du temps.
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    // --- EXPORT PRODUITS ---
    #[Route('/export/csv', name: 'app_product_export_csv', methods: ['GET'])]
    public function exportCsv(ProductRepository $productRepository, CsvExporter $csvExporter): Response
    {
        $products = $productRepository->findAll();
        $csvContent = $csvExporter->exportProductsToCsv($products);

        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export_produits.csv"');

        return $response;
    }
    
    // --- SUPPRIMER UN PRODUIT ---
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ADMIN_ACCESS')]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $token)) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index');
    }
}
