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
        
        $this->updateDatabase();
        $output->writeln("<info>database updated</info>");
        $this->updateProducts($output);
        $output->writeln('done');
    } 
    
    protected function updateDatabase()
    {       
        $sql = "DELETE from sylius_product_model_dropship;";
        $sql2 = "DELETE from sylius_product_picture_dropship;";
        $sql3 = "DELETE from sylius_product_dropship;";
        $this->getEM()->getConnection()->exec($sql);
        $this->getEM()->getConnection()->exec($sql2);
        $this->getEM()->getConnection()->exec($sql3);
        
        $allowedBrands = array('Alexander McQueen', 'Bottega Veneta', 'Burberry', 'Calvin Klein', 'Cavalli B.',
                                'Cerruti', 'Chloe', 'Christian Lacroix', 'DandG', 'Diesel', 'Fendi', 'Ferre', 'Fred Perry',
                                'Gas', 'Gucci', 'Guess', 'Hogan', 'Hugo Boss', 'Just Cavalli', 'Kenzo', 'Michael Kors', 'Moschino',
                                'Nina Ricci', 'Prada', 'Roberto Cavalli', 'Royal Polo',
                                'Sparco', 'Tods', 'Tom Ford', 'Tommy Hilfiger', 'U.S. Polo',
                                'V 1969', 'Versace', 'Versace Jeans');
        foreach($allowedBrands as $brand) {
            $client = $this->getContainer()->get('api_client');
            $request = $client->get('/restful/export/api/products.xml?acceptedlocales=en_US,fr_FR&tag_1='.$brand);
            $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYHOST, false);
            $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYPEER, false);

            $response = $request->send();
            $result = $response->xml();
            foreach($result->items->item as $item) {
                $productDropship = $this->createProductDropship($item);
                $this->getEM()->persist($productDropship);
            }
            $this->getEM()->flush();
            $this->getEM()->clear();        
        }
    }
    
    protected function createProductDropship($item)
    {
        $productDropship = new \App\SyliusProductBundle\Entity\ProductDropship();
        $productDropship->setQuantity($item->availability);
        $productDropship->setBrand($item->brand);
        $productDropship->setCode($item->code);  
        $productDropship->setCurrency('CHF');
        $productDropship->setPartnerProductId($item->id);
        
        foreach ($item->descriptions->description as $description) {
            if ($description->localecode == 'en_US'){
               $productDropship->setDescriptionEn($description->description); 
            }
            if ($description->localecode == 'fr_FR'){
               $productDropship->setDescriptionFr($description->description); 
            }
        }
        foreach ($item->pictures->image as $pictureItem) {
            $picture = new \App\SyliusProductBundle\Entity\ProductPictureDropship();
            $picture->setPath('http://www.brandsdistribution.com'.$pictureItem->url);
            $picture->setProductDropship($productDropship);
            $productDropship->addPicture($picture);
        }
        $productDropship->setRrp((int)$item->streetPrice*100*1.2);
        $productDropship->setActualPrice((int)$item->taxable*100*1.2);        
        $productDropship->setName($item->name);        
        
        foreach ($item->models->model as $modelItem) {
            $model = new \App\SyliusProductBundle\Entity\ProductModelDropship();
            $model->setQuantity($modelItem->availability);
            $model->setCode($modelItem->code);
            $model->setColor($modelItem->color);
            $model->setPartnerModelId($modelItem->id);
            $model->setSize($modelItem->size);
            $model->setRrp((int)$modelItem->streetPrice*100*1.2);
            $model->setActualPrice((int)$modelItem->taxable*100*1.2);
            $model->setProductDropship($productDropship);
            $productDropship->addModel($model);
        }
        
        foreach ($item->tags->tag as $tag) {
            if ($tag->name == 'subcategory') {
                foreach ($tag->value->translations->translation as $translation) {
                    if ($translation->localecode == 'en_US') {
                       $productDropship->setSubCategory(ucfirst(strip_tags($translation->description))); 
                    }
                }
            }
            if ($tag->name == 'category') {
                foreach ($tag->value->translations->translation as $translation) {
                    if ($translation->localecode == 'en_US') {
                        if ($translation->description == 'Men') {
                            $productDropship->setGender('Men');
                            $productDropship->setCategory("Men's Clothing"); 
                        } elseif($translation->description == 'Women') {
                            $productDropship->setGender('Women');
                            $productDropship->setCategory("Women's Clothing"); 
                        } elseif ($translation->description == 'Kids') {
                            $productDropship->setCategory("Kid's Clothing");
                            $productDropship->setGender('Kids');
                        } else {
                            $productDropship->setGender('Unisex');
                            $productDropship->setCategory(ucfirst($translation->description));
                        }
                    }
                }
            }
        }
        return $productDropship;
    }
    
    protected function updateProducts($output)
    {
        $products = $this->getContainer()->get('sylius.repository.product_dropship')->findAll();
        foreach ($products as $product) {
            $this->updateProduct($product, $output);
            foreach ($product->getModels() as $model) {
                $this->updateModel($model, $output);
            } 
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
                $option = new \Sylius\Component\Product\Model\Option();
                $option->setName($productDropship->getCategory().':'.$productDropship->getSubCategory());
                $option->setPresentation('Size');
                $this->getEM()->persist($option);
            } else {
                $optionValue = $optionValueRepository->findOneBy(array('value' => $model->getSize(), 'option' => $option));
            }

            $product->addOption($option);
        
            $variant = new \App\SyliusProductBundle\Entity\ProductVariant();
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
        $variant->setPrice($product->getMasterVariant()->getPrice());
        $variant->setRrp($product->getMasterVariant()->getRrp());
            
        $variantManager = $this->getContainer()->get('sylius.manager.product_variant');
        $variantManager->persist($variant);
        $manager = $this->getContainer()->get('sylius.manager.product');
        $manager->persist($product);
        $manager->flush();   
    }
    
    protected function updateProduct(\App\SyliusProductBundle\Entity\ProductDropship $productDropship, $output)
    {
        $output->writeln('Update product '.$productDropship->getPartnerProductId());
        $repository = $this->getContainer()->get('sylius.repository.product');       

        $product = $repository->findOneBy(array('partnerId' => $productDropship->getPartnerProductId()));
        if (!$product) {
            $product = $repository->createNew();
            $product->setPartnerId($productDropship->getPartnerProductId());
            $product->setSlug($productDropship->getCode());
            $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);
        
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
            if ($productDropship->getModels()->first()->getColor()) {
                $attributes[] = array('name' => 'Color', 'value' => $productDropship->getModels()->first()->getColor());
            }
            $this->addAttributes($product, $attributes);
            $product->setTaxCategory($this->getTaxCategory());            
            $product->setShippingCategory($this->getShippingCategory());
            
            $output->writeln('Adding Master product ');
            $this->addMasterVariant($product, $productDropship);
            $product->setName($productDropship->getName());
            $product->setDescription($productDropship->getDescriptionEn());
        } else {            
            $variant = $product->getMasterVariant();
            $variant->setPrice($this->getPrice($productDropship->getRrp()));
            $variant->setOnHand($productDropship->getQuantity());
            $variant->setRrp($productDropship->getRrp());
        }
        
        $manager = $this->getContainer()->get('sylius.manager.product');
        $manager->persist($product);
        $manager->flush();
        $output->writeln('Flush product '.$productDropship->getCode());
                 
    }
    
    private function getPrice($price)
    {
       return $price - ($price/100*5);
    }
    
    private function getTaxon($parentKey, $child, $childKey)
    {
        $taxonRepository = $this->getContainer()->get('sylius.repository.taxon');
        $taxonParent = $taxonRepository->findOneBy(array('description' => $parentKey));
        $taxon = $taxonRepository->findOneBy(array('description' => $childKey));
        
        if (!$taxon) {
            $taxon = $taxonRepository->createNew();
            $taxon->setName($child);
            $taxon->setParent($taxonParent);
            $taxon->setDescription($childKey);
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
               // $this->getContainer()->get('sylius.manager.product_attribute')->flush($attribute);
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

    protected function addMasterVariant(ProductInterface $product, \App\SyliusProductBundle\Entity\ProductDropship $productDropship)
    {
        $variant = $product->getMasterVariant();
        $variant->setProduct($product);
        $variant->setPrice($this->getPrice($productDropship->getRrp()));
        $variant->setSku($productDropship->getCode());
        $variant->setAvailableOn(new \DateTime());
        $variant->setOnHand($productDropship->getQuantity());
        $variant->setRrp($productDropship->getRrp());
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
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        return $em;
    }
}