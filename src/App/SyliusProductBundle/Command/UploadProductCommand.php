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
        $this->setName('shop-product:upload-products')
            ->setDescription('Imports products from csv file.');
    }
    
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>starting updating</info>");
        $products = $this->getContainer()->get('sylius.repository.product_dropship')->findAll();
        if (count($products) == 0) {
            throw new \Exception('no products to update');
            exit;
        } 
        $sql = "UPDATE sylius_product set deleted_at = null where partner_id in (SELECT partner_product_id from sylius_product_dropship);";
        $this->getEM()->getConnection()->exec($sql);
        
        $this->updateProducts($output);
        $this->deleteProducts();
        $output->writeln('done');
    } 
    
    protected function deleteProducts()
    {
        $sql5 = "UPDATE sylius_product set deleted_at = NOW() where partner_id not in (SELECT partner_product_id from sylius_product_dropship);";
        $this->getEM()->getConnection()->exec($sql5);
        $sql6 = "UPDATE sylius_product set deleted_at = NOW() where id in (select product_id from sylius_product_variant where rrp < 4900 group by product_id);";
        $this->getEM()->getConnection()->exec($sql6);
    }

    protected function updateProducts($output)
    {
        $products = $this->getContainer()->get('sylius.repository.product_dropship')->findAll();
        $productIds = array();
        foreach ($products as $key => $product) {
            $output->writeln("<info>Item </info>".$key);
            if ($product->getRrp() < 4900) {
                continue;
            }
            $productIds[] = $product->getPartnerProductId();
            $this->updateProduct($product, $output);
            foreach ($product->getModels() as $model) {
                $this->updateModel($model, $output);
            }
            $this->getEm()->flush();
            $this->getEM()->clear();
        }        
    }
    
    protected function updateModel(\App\SyliusProductBundle\Entity\ProductModelDropship $model, $output)
    {
        $output->writeln('Update model '.$model->getPartnerModelId());
        $productDropship = $model->getProductDropship();
        
        $repository = $this->getContainer()->get('sylius.repository.product');
        $product = $repository->findOneBy(array('partnerId' => $productDropship->getPartnerProductId())); 
        if (!$product) {
            return null;
        }
        $optionValueRepository = $this->getContainer()->get('sylius.repository.product_option_value'); 

        $variantRepository = $this->getContainer()->get('sylius.repository.product_variant');
        $variant = $variantRepository->findOneBy(array('sku' => $model->getPartnerModelId()));
        if (!$variant) {
            //sozdat option
            $optionRepository = $this->getContainer()->get('sylius.repository.product_option');
            $option = $optionRepository->findOneBy(array('name' => $productDropship->getCategory().':'.$productDropship->getSubCategory()));
            $optionValue = null;
            if (!$option) {
                $option = $optionRepository->createNew();
                $option->setName($productDropship->getCategory().':'.$productDropship->getSubCategory());
                $option->setPresentation('Size');
                $this->getEM()->persist($option);
            } else {
                $optionValue = $optionValueRepository->findOneBy(array('value' => $model->getSize(), 'option' => $option));
            }

            $product->addOption($option);
        
            $variant = $variantRepository->createNew();
            $variant->setProduct($product);
            $product->addVariant($variant);
            $variant->setAvailableOnDemand(false);
            $variant->setSku($model->getPartnerModelId());

            if (!$optionValue) {
                $optionValue = $optionValueRepository->createNew();
                $optionValue->setValue($model->getSize());
                $option->addValue($optionValue);
                $optionValueManager = $this->getContainer()->get('sylius.manager.product_option_value'); 
                $optionValueManager->persist($optionValue);
            }

            $variant->addOption($optionValue);
        }
        
        $variant->setOnHand($model->getQuantity());
        $variant->setPrice((int)$product->getMasterVariant()->getPrice());
        $variant->setRrp($product->getMasterVariant()->getRrp());
            
        $this->getEM()->persist($variant);
        $this->getEM()->persist($product);
        $this->getEM()->flush();   
    }
    
    protected function updateProduct(\App\SyliusProductBundle\Entity\ProductDropship $productDropship, $output)
    {
        $output->writeln('Update product '.$productDropship->getPartnerProductId());
        $repository = $this->getContainer()->get('sylius.repository.product');       

        $product = $repository->findOneBy(array('partnerId' => $productDropship->getPartnerProductId()));
        if (!$product) {
            $product = $repository->createNew();
            if (!$product->getTranslations()->get('en')) {
                $translation = new \Sylius\Component\Core\Model\ProductTranslation();
                $translation->setLocale('en');
                $product->addTranslation($translation);
            }
            if (!$product->getTranslations()->get('fr')) {
                $translation = new \Sylius\Component\Core\Model\ProductTranslation();
                $translation->setLocale('fr');
                $product->addTranslation($translation);
            }
            if (!$product->getTranslations()->get('it')) {
                $translation = new \Sylius\Component\Core\Model\ProductTranslation();
                $translation->setLocale('it');
                $product->addTranslation($translation);
            }
            if (!$product->getTranslations()->get('de')) {
                $translation = new \Sylius\Component\Core\Model\ProductTranslation();
                $translation->setLocale('de');
                $product->addTranslation($translation);
            }
            $product->setCurrentLocale('en');
            $product->setPartnerId($productDropship->getPartnerProductId());
            $product->setSlug($productDropship->getCode());
            
            $output->writeln('Product Taxons');
            $taxons = new \Doctrine\Common\Collections\ArrayCollection();
            if ($productDropship->getBrand()) {
                $taxons->add($this->getTaxon('Brand', $productDropship->getBrand(), 'Brand:'.$productDropship->getBrand()));
            }
            $taxons->add($this->getTaxon('Gender', $productDropship->getGender(), 'Gender:'.$productDropship->getGender()));
            $taxons->add($this->getTaxon('Category', $productDropship->getCategory(), 'Category:'.$productDropship->getCategory()));
            if ($productDropship->getSubCategory()) {
                $taxons->add($this->getTaxon('Category:'.$productDropship->getCategory(), $productDropship->getSubCategory(), 'Category:'.$productDropship->getCategory().':'.$productDropship->getSubCategory()));
            }
            $product->setTaxons($taxons);
            $attributes[] = array('name' => 'Brand', 'value' => $productDropship->getBrand());
            if (isset($productDropship->getModels()->first()) && $productDropship->getModels()->first()->getColor()) {
                $attributes[] = array('name' => 'Color', 'value' => $productDropship->getModels()->first()->getColor());
            }
            $this->addAttributes($product, $attributes);
            $product->setTaxCategory($this->getTaxCategory());            
            $product->setShippingCategory($this->getShippingCategory());
            
            $output->writeln('Adding Master product ');
            $this->addMasterVariant($product, $productDropship);
            $product->setSlug($productDropship->getCode());
            
            $product->setName($productDropship->getName());
            $product->setDescription($productDropship->getDescriptionEn()?$productDropship->getDescriptionEn():' ');
            
            $product->setCurrentLocale('fr');
            $product->setName($productDropship->getName());
            $product->setDescription($productDropship->getDescriptionFr()?$productDropship->getDescriptionFr():' ');
            
            $product->setCurrentLocale('de');
            $product->setName($productDropship->getName());
            $product->setDescription($productDropship->getDescriptionDe()?$productDropship->getDescriptionDe():' ');
            
            $product->setCurrentLocale('it');
            $product->setName($productDropship->getName());
            $product->setDescription($productDropship->getDescriptionIt()?$productDropship->getDescriptionIt():' ');
            
            $this->getEm()->persist($product);
            $this->getEm()->flush();
        } else {        
            foreach($product->getVariants() as $variant) {
                $variant->setOnHand(0);
            }
            $variant = $product->getMasterVariant();
            $variant->setPrice((int)$this->getPrice($productDropship->getActualPrice()));
            $variant->setOnHand($productDropship->getQuantity());
            $this->getEm()->persist($product);
        }
        
        
        $output->writeln('Flush product '.$productDropship->getCode());
                 
    }
    
    private function getPrice($actualPrice)
    {
       return (($actualPrice+10)*1.22)*1.40;
    }
    
    private function getTaxon($parentKey, $child, $childKey)
    {
        $taxonRepository = $this->getContainer()->get('sylius.repository.taxon');
        $taxonParent = $taxonRepository->findOneBy(array('description' => $parentKey));
        $taxon = $taxonRepository->findOneBy(array('description' => $childKey));
        
        if (!$taxon) {
            $taxon = $taxonRepository->createNew();
            if (!$taxon->getTranslations()->get('en')) {
                $translation = new \Sylius\Component\Taxonomy\Model\TaxonTranslation;
                $translation->setLocale('en');
                $taxon->addTranslation($translation);
            }
            if (!$taxon->getTranslations()->get('fr')) {
                $translation = new \Sylius\Component\Taxonomy\Model\TaxonTranslation;
                $translation->setLocale('fr');
                $taxon->addTranslation($translation);
            }
            if (!$taxon->getTranslations()->get('de')) {
                $translation = new \Sylius\Component\Taxonomy\Model\TaxonTranslation;
                $translation->setLocale('de');
                $taxon->addTranslation($translation);
            }
            if (!$taxon->getTranslations()->get('it')) {
                $translation = new \Sylius\Component\Taxonomy\Model\TaxonTranslation;
                $translation->setLocale('it');
                $taxon->addTranslation($translation);
            }
            $taxon->setCurrentLocale('en');
            $taxon->setName($child);
            $taxon->setParent($taxonParent);
            $taxon->setDescription($childKey);
            $taxon->setTaxonomy($taxonParent->getTaxonomy());
            $taxon->setCurrentLocale('fr');
            $taxon->setName($child);
            $taxon->setDescription($childKey);
            $taxon->setCurrentLocale('it');
            $taxon->setName($child);
            $taxon->setDescription($childKey);
            $taxon->setCurrentLocale('de');
            $taxon->setName($child);
            $taxon->setDescription($childKey);
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
        return $repository->findOneByName('Standard');
        
    }

    protected function addMasterVariant(ProductInterface $product, \App\SyliusProductBundle\Entity\ProductDropship $productDropship)
    {
        $variant = $product->getMasterVariant();
        $variant->setProduct($product);
        $variant->setPrice((int)$this->getPrice($productDropship->getActualPrice()));
        $variant->setSku($productDropship->getCode());
        $variant->setAvailableOn(new \DateTime());
        $variant->setOnHand($productDropship->getQuantity());
        $variant->setRrp($productDropship->getRrp()*1.22);
        $variant->setAvailableOnDemand(false);
        $uploader = $this->getContainer()->get('sylius.image_uploader');
        foreach ($variant->getImages() as $image ) {
            $variant->removeImage($image);
        }
        foreach ($productDropship->getPictures() as $key => $picture) {
            if ($key > 3) continue;
            $content = file_get_contents($picture->getPath());
            $path = $this->getContainer()->getParameter('kernel.root_dir').'/../web/uploads/'.$product->getSlug().$key.'.jpg';
            
            file_put_contents($path, $content);

            $image = new ProductVariantImage();
            $image->setFile(new UploadedFile($path, $product->getSlug().$key.'.jpg'));
            $uploader->upload($image);
            $variant->addImage($image);
        }
        $product->setMasterVariant($variant);
    }
    
    protected function getEM()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        return $em;
    }
}