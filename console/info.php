<?php
/**
 * migration:create command for Skeleton Console
 *
 * @author Gerry Demaret <gerry@tigron.be>
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */

namespace Skeleton\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Skeleton\Container\Control\Container;

class Container_Control_Info extends \Skeleton\Console\Command {

	/**
	 * Configure the Create command
	 *
	 * @access protected
	 */
	protected function configure() {
		$this->setName('container:info');
		$this->setDescription('Get info of a paired container');
		$this->addArgument('container', InputArgument::REQUIRED, 'The name of the container');
	}

	/**
	 * Execute the Command
	 *
	 * @access protected
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$container_name = $input->getArgument('container');
		try {
			$container = Container::get_by_name($container_name);
		} catch (\Skeleton\Container\Control\Exception\Container $e) {
			$output->writeln('<error>Container with name ' . $container_name . ' not found</error>');
			return 1;
		}
		$info = $container->info();
		$output->writeln('Name: ' . $container_name);
		$output->writeln('Endpoint: ' . $container->endpoint);
		$output->writeln('Provisioned services: ');
		foreach ($info['services'] as $service) {
			$output->writeln("\t" . '- ' . $service);
		}

		return 0;
	}
}
