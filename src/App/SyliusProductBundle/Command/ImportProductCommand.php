<?php
namespace App\SyliusProductBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImportProductCommand extends ContainerAwareCommand
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
    } 
    
    protected function updateDatabase()
    {       
        $sql = "DELETE from sylius_product_model_dropship;";
        $sql2 = "DELETE from sylius_product_picture_dropship;";
        $sql3 = "DELETE from sylius_product_dropship;";
        $this->getEM()->getConnection()->exec($sql2);
        $this->getEM()->getConnection()->exec($sql);
        $this->getEM()->getConnection()->exec($sql3);
        
        $allowedBrands = array('Alexander McQueen', 'Ana Lublin','Avirex', 'Bottega Veneta', 'Burberry', 'Calvin Klein', 'Cavalli B.', 'Cavalli Class',
                                'Cerruti', 'Chloe', 'Christian Lacroix', 'DandG', 'Diesel', 'Fendi', 'Ferre', 'Fred Perry',
                                'Gas', 'Geographical Norway', 'Gucci','Giuseppe Zanotti' ,'Guess', 'Hogan', 'Hugo Boss', 'Jessica Simpson', 'Just Cavalli', 'Kenzo', 'Made in Italia', 'Michael Kors', 'Moschino',
                                'Nina Ricci', 'Prada', 'Rene Caovilla', 'Richmond','Rosso Fiorentino', 'Roberto Cavalli', 'Rochas', 'Royal Polo',
                                'Sergio Rossi', 'Sparco', 'Tods', 'Tom Ford', 'Tommy Hilfiger', 'U.S. Polo',
                                'V 1969', 'Versace', 'Versace Jeans');
        foreach($allowedBrands as $brand) {
            $client = $this->getContainer()->get('api_client');
            $request = $client->get('/restful/export/api/products.xml?acceptedlocales=en_US,fr_FR,it_IT,de_DE&tag_1='.$brand);
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
        $productDropship->setCurrency('EUR');
        $productDropship->setPartnerProductId($item->id);
        
        foreach ($item->descriptions->description as $description) {
            if ($description->localecode == 'en_US'){
               $productDropship->setDescriptionEn($description->description); 
            }
            if ($description->localecode == 'fr_FR'){
               $productDropship->setDescriptionFr($description->description); 
            }
            if ($description->localecode == 'de_DE'){
               $productDropship->setDescriptionDe($description->description); 
            }
            if ($description->localecode == 'it_IT'){
               $productDropship->setDescriptionIt($description->description); 
            }
        }
        foreach ($item->pictures->image as $pictureItem) {
            $picture = new \App\SyliusProductBundle\Entity\ProductPictureDropship();
            $picture->setPath('http://www.brandsdistribution.com'.$pictureItem->url);
            $picture->setProductDropship($productDropship);
            $productDropship->addPicture($picture);
        }
        $productDropship->setRrp((int)$item->streetPrice*100);
        $productDropship->setActualPrice((int)$item->taxable*100);        
        $productDropship->setName($item->name);        
        
        foreach ($item->models->model as $modelItem) {
            $model = new \App\SyliusProductBundle\Entity\ProductModelDropship();
            $model->setQuantity($modelItem->availability);
            $model->setCode($modelItem->code);
            $model->setColor($modelItem->color);
            $model->setPartnerModelId($modelItem->id);
            $model->setSize($modelItem->size);
            $model->setRrp((int)$modelItem->streetPrice*100);
            $model->setActualPrice((int)$modelItem->taxable*100);
            $model->setProductDropship($productDropship);
            $productDropship->addModel($model);
        }
        
        foreach ($item->tags->tag as $tag) {
            if ($tag->name == 'subcategory') {
                foreach ($tag->value->translations->translation as $translation) {
                    if ($translation->localecode == 'en_US') {
                       $productDropship->setSubCategory(ucfirst(strip_tags($translation->description))); 
                    }
                    if ($translation->localecode == 'fr_FR'){
                       $productDropship->setSubCategoryFr(ucfirst(strip_tags($translation->description)));  
                    }
                    if ($translation->localecode == 'it_IT'){
                       $productDropship->setSubCategoryIt(ucfirst(strip_tags($translation->description)));  
                    }
                    if ($translation->localecode == 'de_DE'){
                       $productDropship->setSubCategoryDe(ucfirst(strip_tags($translation->description)));  
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
                            $productDropship->setCategory("Kids Clothing");
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
    
    protected function getEM()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        return $em;
    }
}