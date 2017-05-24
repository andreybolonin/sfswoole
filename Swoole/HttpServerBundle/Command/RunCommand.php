<?php
namespace Swoole\HttpServerBundle\Command;


use Swoole\Http\Response;
use Swoole\Http\Server;
use Swoole\HttpServerBundle\Http\Http;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

class RunCommand extends ServerCommand
{
	/** @var $kernel Kernel */
	protected $kernel = NULL;
	/** @var $server Server */
	protected $server = NULL;
	protected $env    = 'dev';
	protected $debug  = true;

/*	public function __construct($name = NULL)
	{
		parent::__construct($name);
		$this->kernel = new \AppKernel('dev',true);
		Debug::enable();

	}*/

	protected function configure()
	{
		$this->setName('swoole:run')
			->setDescription('run swoole http server in debug mode')
			->setDefinition(array(
				new InputOption('host', null, InputOption::VALUE_OPTIONAL, 'Host for server', '127.0.0.1'),
				new InputOption('port', null, InputOption::VALUE_OPTIONAL, 'Port for server', 2345),
				new InputOption('docroot', null, InputOption::VALUE_OPTIONAL, 'Document root', NULL),
			));
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->init( $input,  $output);
		$this->server = new Server($input->getOption('host'),$input->getOption('port'));
		$this->server->on('start',[$this,'onStart']);
		$this->server->on('ManagerStart',[$this,'onManagerStart']);
		$this->server->on('WorkerStart',[$this,'onWorkerStart']);
		$this->server->on('Connect',[$this,'onConnect']);
		$this->server->on('request',[$this,'onRequest']);
		$this->server->start();
	}

	// 每次连接时(相当于每个浏览器第一次打开页面时)执行一次, reload 时连接不会断开, 也就不会再次触发该事件
	public function onConnect(Server $server,$fd,$reactorThreadId)
	{
		$this->kernel = new \AppKernel('dev',true);
		Debug::enable();

	}

	// 每次打开链接页面默认都是接收两个请求, 一个是正常的数据请求, 一个 favicon.ico 的请求
	// 每次请求都只会执行这里面的代码，不用再初始化框架内核，运行性能大大提高！
	public function onRequest(\Swoole\Http\Request $swRequest,Response $swResponse)
	{
		$this->server->reload();
		//$kernel = $this->getContainer()->get('kernel');
		//$this->autoReload();
		/** @var Request $sfRequest */
		$sfRequest = Http::createSfRequest($swRequest);
		$sfResponse = $this->kernel->handle($sfRequest);
		$swResponse->end(Http::createSwResponse($swResponse,$sfResponse));
		$this->kernel->terminate($sfRequest,$sfResponse);
	}

	// 服务器启动时执行一次
	public function onStart(Server $server)
	{
	}

	// 服务器启动时执行一次
	public function onManagerStart(Server $server)
	{

	}

	// 每个 Worker 进程启动或重启时都会执行
	public function onWorkerStart(Server $server,$workerId)
	{

	}


	public function onShutdown(Server $server)
	{
		unlink($this->pid_file);
	}

	public function autoReload()
	{
		$env = $this->getContainer()->getParameter('kernel.environment');
		$this->change_time = $this->change_time?$this->change_time:time();
		$change_time = $this->isChange();
		if($env=='dev' && $change_time)
		{
			$this->change_time = $change_time;
			$this->server->reload();
		}
	}

	public function init(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);

		$address = $input->getOption('host');
		$this->address = $address;
		if (false === strpos($address, ':')) {
			$address = $address.':'.$input->getOption('port');
		}

		if ($this->isOtherServerProcessRunning($address))
		{
			$io->error(sprintf('A process is already listening on http://%s.', $address));

			return 1;
		}

		$io->success(sprintf('Server running on http://%s', $address));
		$io->comment('Quit the server with CONTROL-C.');
		return 1;
	}
}