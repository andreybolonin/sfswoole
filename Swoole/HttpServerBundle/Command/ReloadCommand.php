<?php
namespace Swoole\HttpServerBundle\Command;



use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ReloadCommand extends ServerCommand
{
	protected function configure()
	{
		$this->setName('swoole:reload')->setDescription('reload swoole http server');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$pid = $this->getPid();
		exec("kill -USR1 {$pid}");
	}

}