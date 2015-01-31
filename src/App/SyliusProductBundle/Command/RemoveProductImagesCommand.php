<?php
namespace App\SyliusProductBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class RemoveProductImagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('shop-product:remove-images')
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
        $repository = $this->getContainer()->get('sylius.repository.product');
        $products = $repository->findAll();
        $filesystem = $this->getContainer()->get('knp_gaufrette.filesystem_map')->get('sylius_image');
        foreach ($products as $product) {
            $variant = $product->getMasterVariant();
            foreach($variant->getImages() as $image) {
                try {
                    $filesystem->read($image->getPath());
                    $output->writeln("<info>yes</info>");
                } catch(\RuntimeException $e) {
                    $output->writeln("<error>NO</error>");
                    $variant->removeImage($image);
                    $this->getEM()->persist($product);
                    $this->getEM()->flush();
                }
            }
        }

        $output->writeln('done');
    } 
    
    
    
    
    protected function getEM()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        return $em;
    }
}