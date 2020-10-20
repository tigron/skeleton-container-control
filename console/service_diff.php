<?php
/**
 * container:rename command for Skeleton Console
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
use Skeleton\Container\Control\Container;
use Skeleton\Container\Control\Service;

class Container_Control_Service_Diff extends \Skeleton\Console\Command {

	/**
	 * Configure the Rename command
	 *
	 * @access protected
	 */
	protected function configure() {
		$this->setName('service:diff');
		$this->setDescription('Show differences between the local and remote service, if any');
		$this->addArgument('container', InputArgument::REQUIRED, 'The name of the container');
		$this->addArgument('service', InputArgument::REQUIRED, 'The name of the service');
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
		$service_name = $input->getArgument('service');

		try {
			$container = Container::get_by_name($container_name);
		} catch (\Skeleton\Container\Control\Exception\Container $e) {
			$output->writeln('<error>Container with name ' . $container_name . ' not found</error>');
			return 1;
		}

		try {
			$service = Service::get_by_name($service_name);
		} catch (\Skeleton\Container\Control\Exception\Service $e) {
			$output->writeln('<error>Service with name ' . $service_name . ' not found</error>');
			return 1;
		}

		$diff_files = $container->diff($service);

		if (count($diff_files) === 0) {
			$output->writeln('<info>Remote service is identical</info>');
		} else {
			$output->writeln('<error>Remote service differs</error>');

			foreach ($diff_files as $diff_file => $diff) {
				$output->writeln('file: ' . $diff_file);
				$output->writeln($diff);
			}
		}


/*
		$info = $container->info();
		$output->writeln('Name: ' . $container_name);
		$output->writeln('Endpoint: ' . $container->endpoint);
		$output->writeln('Provisioned services: ');
		foreach ($info['services'] as $service) {
			$output->writeln("\t" . '- ' . $service);
		}
*/

		return 0;
	}
}
