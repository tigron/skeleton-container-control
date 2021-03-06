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
use Skeleton\Container\Control\Container;
use Skeleton\Container\Control\Util;

class Container_Control_Pair extends \Skeleton\Console\Command {

	/**
	 * Configure the Create command
	 *
	 * @access protected
	 */
	protected function configure() {
		$this->setName('container:pair');
		$this->setDescription('Pair with a new container');
		$this->addArgument('endpoint', InputArgument::REQUIRED, 'The endpoint of your container');
	}

	/**
	 * Execute the Command
	 *
	 * @access protected
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$endpoint = $input->getArgument('endpoint');
		$container = Container::pair($endpoint);
		$output->writeln('Paired with new container: ' . $container->name);

		if ($container->ssl_certificate !== null) {
			$info = Util::get_certificate_info($container->ssl_certificate);
			$output->writeln('<comment>Using self-signed SSL certificate:</comment>');
			$output->writeln('<comment> - subject: ' . $info['subject'] . '</comment>');
			$output->writeln('<comment> - serial: ' . $info['serial_number'] . '</comment>');
			$output->writeln('<comment> - valid from: ' . $info['valid_from'] . '</comment>');
			$output->writeln('<comment> - valid to: ' . $info['valid_to'] . '</comment>');
		}

		return 0;
	}

}
