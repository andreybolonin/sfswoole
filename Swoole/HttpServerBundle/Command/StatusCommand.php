<?php
namespace Swoole\HttpServerBundle\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class StatusCommand extends ServerCommand
{
	protected function configure()
	{
		$this->setName('swoole:status')->setDescription('check swoole http server status');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$pid = $this->getPid();
		if($pid)
		{
			$output->writeln('swoole http server is running, pid is '.$pid);
		}
		else
		{
			$output->writeln('swoole http server is not run');
		}
	}


}