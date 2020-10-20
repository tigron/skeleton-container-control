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

class Container_Control_Rename extends \Skeleton\Console\Command {

	/**
	 * Configure the Rename command
	 *
	 * @access protected
	 */
	protected function configure() {
		$this->setName('container:rename');
		$this->setDescription('Rename a container');
		$this->addArgument('current_name', InputArgument::REQUIRED, 'The current name of the container');
		$this->addArgument('new_name', InputArgument::REQUIRED, 'The new name of the container');
	}

	/**
	 * Execute the Command
	 *
	 * @access protected
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$current_name = $input->getArgument('current_name');
		$new_name = $input->getArgument('new_name');

		try {
			$container = Container::get_by_name($current_name);
		} catch (\Skeleton\Container\Control\Exception\Container $e) {
			$output->writeln('<error>Container with name ' . $current_name . ' not found</error>');
			return 1;
		}

		$container->name = $new_name;
		$container->save();

		$output->writeln('Container ' . $current_name . ' renamed to ' . $new_name);
		$output->writeln('<comment>Do not forget to update existing calls</comment>');

		return 0;
	}
}
