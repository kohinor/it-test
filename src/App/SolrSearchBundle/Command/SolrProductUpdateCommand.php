<?php
namespace App\SolrSearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SolrProductUpdateCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('Solr:product-indexing')->setDescription('reindexing');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getContainer()->get('solarium.client');
        $update = $client->createUpdate();
        $deletedProducts = $this->getContainer()->get('sylius.repository.product')->findDeletedProducts();
        foreach ($deletedProducts as $product) {
            $update->addDeleteById($product->getId());
        }
        $result = $client->update($update);

        $buffer = $client->getPlugin('bufferedadd');
        $buffer->setBufferSize(100);

        $products = $this->getContainer()->get('sylius.repository.product')->findActiveProducts(); 
        
        $output->writeln(sprintf("Found <info> %d </info> products", count($products)));
        $step = 0;
        foreach($products as $product) {
            if (!$product->getMasterVariant()) continue;                
                try {
                    $output->writeln('<info>productId</info> '. $product->getSlug());
                    $document = $this->getContainer()->get('solr.update.service')->getSolrDocument($update->createDocument(), $product);
                    $buffer->addDocument($document);
                    $step = $step+1;
                    if ($step == 1000) {
                        $step = 0;
                    }
                } catch(\Solarium_Client_HttpException $e) {
                   $output->writeln('<error>error</error> '. $e->getMessage());
                }            
        };
        $buffer->commit();
        $buffer->flush();
        $output->writeln('done');
    }
}
