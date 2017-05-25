<?php
namespace Swoole\HttpServerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class StopCommand extends ServerCommand
{
	protected function configure()
	{
		$this->setName('swoole:stop')->setDescription('stop swoole http server');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$pid = $this->getPid();
		exec("kill {$pid}");
		unlink($this->getPidFile());
	}


}