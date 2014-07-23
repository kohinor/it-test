<?php
namespace App\SyliusProductBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class AddProductGroupsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('shop-product:add-product-groups')
            ->setDescription('Add products to groups.');
    }


    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>starting updating</info>");
        $groupRepository = $this->getContainer()->get('sylius.repository.product.group');
        $productRepository = $this->getContainer()->get('sylius.repository.product');
        $manager = $this->getContainer()->get('doctrine')->getManager();
        $products = $productRepository->findUniqueProducts(); 
        //die(print count($products));
        foreach ($products as $product) {
            if (!$product->getMasterVariant()) {
                print $product->getId();
                continue;
            }
            $price = $product->getMasterVariant()->getPrice();
            $group = $groupRepository->findOneBy(array('name' => $product->getName().$price));
            if (!$group) {
                $group = new \App\SyliusProductBundle\Entity\ProductGroup();
            }
            $group->setName($product->getName().$price);
            $manager->persist($group);
            
            $groupProducts = $productRepository->findBy(array('name' => $product->getName()));
            print $group->getName().':'.$product->getName().':'.count($groupProducts).'<br>';
            foreach ($groupProducts as $groupProduct) {
                if ($groupProduct->getMasterVariant()->getPrice() != $price) continue;
                $groupProduct->setGroup($group);
                $manager->persist($groupProduct);
            }
            $manager->flush();
            
        }
        $output->writeln('done');
    } 
}