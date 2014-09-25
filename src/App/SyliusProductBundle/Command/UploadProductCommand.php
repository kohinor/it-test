<?php
namespace App\SyliusProductBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class UploadProductCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('shop-product:import-products')
            ->setDescription('Imports products from csv file.')
            ->addArgument('file');
    }


    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $output->writeln("<info>starting updating</info>");
       // $file = '/home/nkapralova/Downloads/products.csv';
        if (($handle = fopen($file, "r")) !== false) {
            $data = fgetcsv($handle, 5000, ",");
            while (($data = fgetcsv($handle, 5000, ",")) !== false) {
                if ($data[0] == 'PRODUCT') {
                    $this->updateProduct($data, $output);
                }
                if ($data[0] == 'MODEL') {
                    $this->updateModel($data, $output);
                }
            }
        }
        $output->writeln('done');
    } 
    protected function updateModel($data, $output)
    {
        $output->writeln('Update model '.$data[13]);
        $productId = $data[1];
        $modelId = $data[13];
        $modelBarcode = $data[14] ? $data[14] : 'n/a';
        $modelSize= trim($data[15]);
        $modelOnHand = $data[16];
        
        $repository = $this->getContainer()->get('sylius.repository.product');
        $product = $repository->findOneBy(array('partnerId' => $productId)); 
        if (!$product) {
            return null;
        }
        $optionRepository = $this->getContainer()->get('sylius.repository.product_option');
        if (in_array($modelSize, array('S', 'M', 'L', 'XL', 'XXL', 'XXXL'))) {
            $option = $optionRepository->findOneBy(array('name' => 'Leter Size'));
            $product->addOption($option);
        } else {
            $option = $optionRepository->findOneBy(array('name' => 'Number size'));
            $product->addOption($option);
        }

        $variantRepository = $this->getContainer()->get('sylius.repository.product_variant');
        $variant = $variantRepository->findOneBy(array('sku' => $modelId));
        if (!$variant) {
            $variant = new \App\SyliusProductBundle\Entity\ProductVariant();
            $variant->setProduct($product);
            $product->addVariant($variant);
        }
        
        $variant->setBarcode($modelBarcode);
        $variant->setSku($modelId);
        $variant->setOnHand($modelOnHand);
        
        $optionValueRepository = $this->getContainer()->get('sylius.repository.product_option_value'); 
        $optionValue = $optionValueRepository->findOneBy(array('value' => $modelSize));
        
        if (!$optionValue) {
            $optionValue = $optionValueRepository->createNew();
            $optionValue->setValue($modelSize);
            $option->addValue($optionValue);
            $optionValueManager = $this->getContainer()->get('sylius.manager.product_option_value'); 
            $optionValueManager->persist($optionValue);
            $optionValueManager->flush();
        }
        
        $variant->addOption($optionValue);
        $variant->setPrice($product->getMasterVariant()->getPrice());
        $variant->setRrp($product->getMasterVariant()->getPrice());
        $variantManager = $this->getContainer()->get('sylius.manager.product_variant');
        $variantManager->persist($variant);
        $manager = $this->getContainer()->get('sylius.manager.product');
        $manager->persist($product);
        $manager->flush();
        
    }
    protected function updateProduct($data, $output)
    {
        $output->writeln('Update product '.$data[4]);
        $productId = $data[1];
        $brand = $data[2];
        $productName = $data[3];
        $slug = $data[4];

        if ($slug) {
            if (strstr($slug, 'lady')) {
                $gender = 'Women';
                $color = explode('_', end(explode('lady', $slug)));
            } elseif (strstr($slug, 'woman')) {
                $gender = 'Women';
                $color = explode('_', end(explode('woman', $slug)));
            } elseif (strstr($slug, 'man')) {
                $gender = 'Men';
                $color = explode('_', end(explode('man', $slug)));
            } else {
                $color = end(explode('_', $slug));
            }
        }
        $description = str_replace('ᐧ', '<br />', $data[8]);

        $repository = $this->getContainer()->get('sylius.repository.product');
       

        $product = $repository->findOneBy(array('partnerId' => $productId));
        if (!$product) {
            $product = $repository->createNew();
        }
        $product->setPartnerId($productId);
        $product->setSlug(strtolower($slug));
        $product->setName($productName);
        $product->setDescription($description);
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);
        
        $output->writeln('Product Settings');
        $taxons = new \Doctrine\Common\Collections\ArrayCollection();
        $taxons->add($this->getTaxon('Brand', $brand));
        $taxons->add($this->getTaxon('Gender', $gender));
        $product->setTaxons($taxons);

        $attributes[] = array('name' => 'Brand', 'value' => $brand);
        if (is_array($color)) {
            foreach ($color as $singleColor) {
                if ($singleColor != '') {
                    $attributes[] = array('name' => 'Color', 'value' => $singleColor);
                }
            }
        } else {
            $attributes[] = array('name' => 'Color', 'value' => $color);
        }
        
        $this->addAttributes($product, $attributes);
        $product->setTaxCategory($this->getTaxCategory());            
        $product->setShippingCategory($this->getShippingCategory());
        
        $this->addMasterVariant($product, $data);
        $manager = $this->getContainer()->get('sylius.manager.product');
        $manager->persist($product);
        $manager->flush();
                 
    }
    
    private function getTaxon($parent, $child)
    {
        $taxonRepository = $this->getContainer()->get('sylius.repository.taxon');
        $taxonParent = $taxonRepository->findOneBy(array('name' => $parent));
        $taxon = $taxonRepository->findOneBy(array('name' => $child));
        
        if (!$taxon) {
            $taxon = $taxonRepository->createNew();
            $taxon->setName($child);
            $taxon->setParent($taxonParent);
            $taxon->setTaxonomy($taxonParent->getTaxonomy());
            $manager = $this->getContainer()->get('sylius.manager.taxon');

            $manager->persist($taxon);
            $manager->flush();
        }
        return $taxon;
    }  
    
    private function addAttributes(ProductInterface $product, $attributes)
    {
        foreach ($product->getAttributes() as $removeAttribute) {
            $product->removeAttribute($removeAttribute);
        }
        $productAttributes = new \Doctrine\Common\Collections\ArrayCollection();
        $attributeRepository = $this->getContainer()->get('sylius.repository.product_attribute');
        foreach ($attributes as $attributeArr) {
            $attribute = $attributeRepository->findOneBy(array('presentation' => $attributeArr['name']));
            if (null === $attribute) {
                $attribute = $attributeRepository->createNew();

                $attribute->setName($attributeArr['name']);
                $attribute->setPresentation($attributeArr['name']);

                $this->getContainer()->get('sylius.manager.product_attribute')->persist($attribute);
                $this->getContainer()->get('sylius.manager.product_attribute')->flush($attribute);
            }

            $attributeValue = $this->getContainer()->get('sylius.repository.product_attribute_value')->createNew();

            $attributeValue->setAttribute($attribute);
            $attributeValue->setValue($attributeArr['value']);
            $productAttributes->add($attributeValue);
        }
        $product->setAttributes($productAttributes);
    }
    
    private function getTaxCategory()
    {
        $repository = $this->getContainer()->get('sylius.repository.tax_category');
        return $repository->findOneByName('Taxable goods');
    }
    
    private function getShippingCategory()
    {
        $repository = $this->getContainer()->get('sylius.repository.shipping_category');
        return $repository->findOneByName('First 1-3 days');
        
    }

    protected function addMasterVariant(ProductInterface $product, $data)
    {
        $productPrice = $data[6];
        $productImages = array($data[10], $data[11], $data[12]);
        $variant = $product->getMasterVariant();
        $variant->setProduct($product);
        $variant->setPrice($productPrice*100);
        $variant->setSku('none');
        $variant->setAvailableOn(new \DateTime());
        $variant->setOnHand(0);
        $variant->setBarcode($data[14] ? $data[14] : 'n/a');
        $variant->setRrp($productPrice*100);

        $uploader = $this->getContainer()->get('sylius.image_uploader');
        foreach ($variant->getImages() as $image ) {
            $variant->removeImage($image);
        }
        foreach ($productImages as $key => $imageLink) {
            
            $content = file_get_contents($imageLink);
            $path = $this->getContainer()->getParameter('kernel.root_dir').'/../web/uploads/'.$product->getSlug().$key.'.jpg';
            
            file_put_contents($path, $content);

            $image = new ProductVariantImage();
            $image->setFile(new UploadedFile($path, $product->getSlug().$key.'.jpg'));
            $uploader->upload($image);
            $variant->addImage($image);
        }
        $product->setMasterVariant($variant);
    }
}