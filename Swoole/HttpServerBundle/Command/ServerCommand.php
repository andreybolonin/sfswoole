<?php

namespace Swoole\HttpServerBundle\Command;

use Swoole\Http\Server;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

abstract class ServerCommand extends ContainerAwareCommand
{
    /** @var $kernel Kernel */
    protected $kernel = null;
    /** @var $server Server */
    protected $server = null;
    protected $change_time = null;
    protected $pid_file = '';

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if (defined('HHVM_VERSION')) {
            return false;
        }

        if (!class_exists('Symfony\Component\Process\Process')) {
            return false;
        }

        return parent::isEnabled();
    }

    /**
     * Determines the name of the lock file for a particular PHP web server process.
     *
     * @param string $address An address/port tuple
     *
     * @return string The filename
     */
    protected function getLockFile($address)
    {
        return sys_get_temp_dir().'/'.strtr($address, '.:', '--').'.pid';
    }

    protected function isOtherServerProcessRunning($address)
    {
        $lockFile = $this->getLockFile($address);

        if (file_exists($lockFile)) {
            return true;
        }

        $pos = strrpos($address, ':');
        $hostname = substr($address, 0, $pos);
        $port = substr($address, $pos + 1);

        $fp = @fsockopen($hostname, $port, $errno, $errstr, 5);

        if (false !== $fp) {
            fclose($fp);

            return true;
        }

        return false;
    }

    public function isChange()
    {
        $root_dir = $this->getContainer()->getParameter('kernel.root_dir');
        $src_dir = $root_dir.'/../src';
        $dir_iterator = new \RecursiveDirectoryIterator($src_dir);
        $iterator = new \RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            if ($this->change_time < $file->getMTime()) {
                return $file->getMTime();
                break;
            }
        }
        $app_dir = $root_dir.'/../app';
        $dir_iterator = new \RecursiveDirectoryIterator($app_dir);
        $iterator = new \RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            if ($this->change_time < $file->getMTime()) {
                return $file->getMTime();
                break;
            }
        }

        return false;
    }

    public function writePid()
    {
        $pid_file = fopen($this->pid_file, 'w');
        fwrite($pid_file, $this->server->master_pid);
        fclose($pid_file);
    }

    public function getPid()
    {
        $pid_file = $this->getPidFile();

        if (!file_exists($pid_file)) {
            return false;
        }
        $f = fopen($pid_file, 'r');
        $line = fgets($f);
        fclose($f);

        return $line;
    }

    public function getPidFile()
    {
        $root_dir = $this->getContainer()->getParameter('kernel.root_dir');

        return  $root_dir.'/../web'.'/swoole_http_server.pid';
    }
}
