<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;


class DeployVHostCommand extends Command
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

    protected static $defaultName = 'deploy:nginx';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $template = $this->twig->render('nginx/vhost.twig', [
            'domain' => getenv('DOMAIN')
        ]);

        file_put_contents('/etc/nginx/sites-enabled/ltfs.conf', $template);

        shell_exec('/usr/sbin/service nginx restart');

        $io->success('Done');
    }
}
