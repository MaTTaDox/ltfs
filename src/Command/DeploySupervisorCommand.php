<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;


class DeploySupervisorCommand extends Command
{
    /**
     * @var Environment
     */
    protected $twig;
    /**
     * @var KernelInterface
     */
    protected $kernel;

    public function __construct(Environment $twig, KernelInterface $kernel, string $name = null)
    {
        parent::__construct($name);
        $this->twig = $twig;
        $this->kernel = $kernel;
    }

    protected static $defaultName = 'deploy:supervisor';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $template = $this->twig->render('supervisor/config.twig', [
            'path' => $this->kernel->getProjectDir()
        ]);

        file_put_contents('/etc/supervisor/conf.d/jobQueueWorker.conf', $template);

        shell_exec('/usr/sbin/service supervisor restart');

        $io->success('Done');
    }
}
