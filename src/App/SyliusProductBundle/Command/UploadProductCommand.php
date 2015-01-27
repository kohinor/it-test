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
            ->addArgument('file')
            ->addArgument('language');
    }


    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $language = $input->getArgument('language');
        $output->writeln("<info>starting updating</info>");
       /* if (($handle = fopen($file, "r")) !== false) {
            $sql = "TRUNCATE sylius_product_dropship";
            $this->getEM()->getConnection()->exec($sql);
            $i = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $i++;
                if ($i < 4) continue;
                $productDropship = new \App\SyliusProductBundle\Entity\ProductDropship();
                $productDropship->setProductType($data[0]);
                $productDropship->setPartnerProductId($data[1]);
                $productDropship->setBrand($data[2]);
                if ($language == 'fr') {
                    $productDropship->setNameFr($data[3]);
                } else {
                    $productDropship->setNameEn($data[3]);
                }
                $productDropship->setCode($data[4]);
                if ($data[5]) {
                    $productDropship->setQuantity($data[5]);
                }
                $productDropship->setRrp($data[6]*100);
                $productDropship->setActualPrice($data[8]*100);
                if ($language == 'fr') {
                    $productDropship->setDescriptionFr($data[9]);
                } else {
                    $productDropship->setDescriptionEn($data[9]);
                }
                $productDropship->setPicture1($data[11]);
                $productDropship->setPicture2($data[12]);
                $productDropship->setPicture3($data[13]);
                $productDropship->setFirm($data[14]);
                if ($data[15] == 'Men') {
                    $productDropship->setCategory("Men's Clothing");
                } elseif ($data['15'] == 'Women') {
                    $productDropship->setCategory("Women's Clothing");
                } elseif ($data['15'] == 'Kids') {
                    $productDropship->setCategory("Kid's Clothing");
                } else {
                    $productDropship->setCategory(ucfirst($data['15']));
                }
                $productDropship->setSubCategory(ucfirst($data[16]));
                $productDropship->setColor($data[20]);
                $productDropship->setGender($data[28]);
                $productDropship->setPartnerModelId($data[30]);
                $productDropship->setBarcode($data[31]);
                $productDropship->setSize($data[32]);
                if ($data[33]) {
                    $productDropship->setQuantity($data[33]);
                }
                
                $this->getEM()->persist($productDropship);
                if ($i >= 200) {
                    $this->getEM()->flush();
                    $this->getEM()->clear();
                }
                
            }
            
        }
        $this->getEM()->flush();
        $this->getEM()->clear();
*/
        $this->updateProducts($output);
        $output->writeln('done');
    } 
    protected function updateProducts($output)
    {
        $allowedBrands = array('Alexander McQueen', 'Bottega Veneta',
                                'Burberry', 'Calvin Klein', 'Cavalli B.',
                                'Cerruti', 'Chloe', 'Christian Lacroix',
                                'D&G', 'Diesel', 'Fendi', 'Ferre', 'Fred Perry',
                                'Gas', 'Gucci', 'Guess', 'Hogan', 'Hugo Boss',
                                'Just Cavalli', 'Kenzo', 'Michael Kors', 'Moschino',
                                'Nina Ricci', 'Prada', 'Roberto Cavalli', 'Royal Polo',
                                'Sparco', 'Tods', 'Tom Ford', 'Tommy Hilfiger', 'U.S. Polo',
                                'V 1969', 'Versace', 'Versace Jeans');
        
        $qb = $this->getEM()->createQueryBuilder();
        $qb->add('select', 'p')->add('from', '\App\SyliusProductBundle\Entity\ProductDropship p');
        $qb->where('p.brand IN (:brands)')->setParameter('brands', $allowedBrands);
            
        $query = $qb->getQuery();
        $products = $query->getResult();
        foreach ($products as $product) {
            $this->updateProduct($product, $output);
            $qb = $this->getEM()->createQueryBuilder();
            $qb->add('select', 'p')->add('from', '\App\SyliusProductBundle\Entity\ProductDropship p');
            $qb->where('p.partnerProductId = :partnerId')->setParameter('partnerId', $product->getPartnerProductId());
            $models = $qb->getQuery()->getResult();
            foreach ($models as $model) {
                if ($model->getProductType() == 'MODEL') {
                    $this->updateModel($model, $output);
                }
            } 
        }
    }
    
    protected function updateModel(\App\SyliusProductBundle\Entity\ProductDropship $model, $output)
    {
        $output->writeln('Update model '.$model->getPartnerModelId());
        
        $repository = $this->getContainer()->get('sylius.repository.product');
        $product = $repository->findOneBy(array('partnerId' => $model->getPartnerProductId())); 
        if (!$product) {
            return null;
        }
        $optionValueRepository = $this->getContainer()->get('sylius.repository.product_option_value'); 
        
        $qb = $this->getEM()->createQueryBuilder();
        $qb->add('select', 'p')->add('from', '\App\SyliusProductBundle\Entity\ProductDropship p');
        $qb->where('p.partnerProductId = :partnerId')->andWhere('p.productType = :partnerType')
            ->setParameter('partnerId', $model->getPartnerProductId())
            ->setParameter('partnerType', 'PRODUCT');
        $query = $qb->getQuery();
        $productDropship = $query->getSingleResult();

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
            $variant->setBarcode($model->getBarcode());
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
            $product->setSlug(strtolower(str_replace(' ', '_', $productDropship->getCode())));
            $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);
        
            $output->writeln('Product Taxons');
            $taxons = new \Doctrine\Common\Collections\ArrayCollection();
            if ($productDropship->getBrand()) {
                $taxons->add($this->getTaxon('Brand', $productDropship->getBrand()));
            }
            $taxons->add($this->getTaxon('Gender', $productDropship->getGender() ? $productDropship->getGender() : 'Unisex'));
            $taxons->add($this->getTaxon('Category', $productDropship->getCategory()));
            if ($productDropship->getSubCategory()) {
                $taxons->add($this->getTaxon($productDropship->getCategory(), $productDropship->getSubCategory()));
            }
            $product->setTaxons($taxons);
            $attributes[] = array('name' => 'Brand', 'value' => $productDropship->getBrand());
            if ($productDropship->getColor()) {
                $attributes[] = array('name' => 'Color', 'value' => $productDropship->getColor());
            }
            $this->addAttributes($product, $attributes);
            $product->setTaxCategory($this->getTaxCategory());            
            $product->setShippingCategory($this->getShippingCategory());
            
            $output->writeln('Adding Master product ');
            $this->addMasterVariant($product, $productDropship);
            $product->setName($productDropship->getNameEn());
            $description = str_replace('ᐧ', '<br />', $productDropship->getDescriptionEn());
            $product->setDescription($description);
        } else {
            $variant = $product->getMasterVariant();
            $variant->setPrice($productDropship->getRrp() - ($productDropship->getRrp()/100*5));
            $variant->setOnHand($productDropship->getQuantity());
            $variant->setRrp($productDropship->getRrp());
            if ($productDropship->getNameFr()) {
                $repository = $this->getEM()->getRepository('Gedmo\\Translatable\\Entity\\Translation');
                $repository->translate($product, 'name', 'fr', $productDropship->getNameFr());
                $description = str_replace('ᐧ', '<br />', $productDropship->getDescriptionFr());
                $repository->translate($product, 'description', 'fr', $description);
            }
        }
        
        $manager = $this->getContainer()->get('sylius.manager.product');
        $manager->persist($product);
        $manager->flush();
        $output->writeln('Flush product '.$productDropship->getPartnerProductId());
                 
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
        $productImages = array($productDropship->getPicture1(), $productDropship->getPicture2(), $productDropship->getPicture3());
        $variant = $product->getMasterVariant();
        $variant->setProduct($product);
        $variant->setPrice($productDropship->getRrp() - ($productDropship->getRrp()/100*5));
        $variant->setSku($productDropship->getCode());
        $variant->setAvailableOn(new \DateTime());
        $variant->setOnHand($productDropship->getQuantity());
        $variant->setBarcode('n/a');
        $variant->setRrp($productDropship->getRrp());
        $variant->setAvailableOnDemand(false);
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
    
    protected function getEM()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        return $em;
    }
}