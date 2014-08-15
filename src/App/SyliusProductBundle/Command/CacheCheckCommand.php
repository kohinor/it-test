<?php
namespace App\SyliusProductBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class CacheCheckCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('cache:check');
    }


    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>starting updating</info>");

        $memcached = new \Memcached();
        $memcached->addServer('127.0.0.1', 11211);

        $memcachedDriver = new \Doctrine\Common\Cache\MemcachedCache();
        $memcachedDriver->setMemcached($memcached);
        $memcachedDriver->save('memcached_id', 'test');
        if ($memcachedDriver->contains('memcached_id')) {
            $output->writeln('memcached is available');
        } else {
            $output->writeln('memcached is available');
        }
        
        $memcache = new \Memcache();
        $memcache->connect('127.0.0.1', 11211);

        $memcacheDriver = new \Doctrine\Common\Cache\MemcacheCache();
        $memcacheDriver->setMemcache($memcache);
        $memcacheDriver->save('memcache_id', 'test');
        if ($memcacheDriver->contains('memcache_id')) {
            $output->writeln('memcache is available');
        } else {
            $output->writeln('memcache is not available');
        }
        
        $apcDriver = new \Doctrine\Common\Cache\ApcCache();
        $apcDriver->save('apc_id', 'test');
        if ($apcDriver->contains('apc_id')) {
            $output->writeln('apc is available');
        } else {
            $output->writeln('apc is not available');
        }

        $xcacheDriver = new \Doctrine\Common\Cache\XcacheCache();
        $xcacheDriver->save('xcache_id', 'test');
        if ($xcacheDriver->contains('xcache_id')) {
            $output->writeln('xcache is available');
        } else {
            $output->writeln('xcache is not available');
        }
        $output->writeln('done');
    } 
}