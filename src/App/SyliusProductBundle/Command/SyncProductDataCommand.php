<?php
namespace App\SyliusProductBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class SyncProductDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('shop-product:sync-product-data');
    }
    
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>starting updating</info>");
        $products = $this->getContainer()->get('sylius.repository.product')->findActiveProducts();
        foreach ($products as $product) {
            $totalAmount = 0;
            foreach ($product->getVariants() as $variant) {
                $totalAmount+=$variant->getOnHand();
                $variant->setPrice($product->getMasterVariant()->getPrice());
                $variant->setRrp($product->getMasterVariant()->getRrp());
            }
            $product->getMasterVariant()->setOnHand($totalAmount);
            $output->writeln("<info>updated</info>".$product->getId());
        }
        $this->getEM()->flush();
    } 
    
    
    
    
    protected function getEM()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        return $em;
    }
}