# skeleton-container-control

## Description

This library enables the communication with a remote server. The remote server
needs to have the skeleton-remote package enabled

## Installation

Installation via composer:

    composer require tigron/skeleton-container-control

## Howto

Initialize the service directory

    \Skeleton\Container\Control\Config::$service_dir = $some_very_cool_directory;

The following skeleton console commands will be available:

    container
      container:deprovision  Deprovision a service from a container
      container:info         Get info of a paired container
      container:list         List all paired containers
      container:pair         Pair with a new container
      container:provision    Provision a container with a new service
      container:unpair       Unpair from a container
    service
      service:list           List all known services

To create a service, create a directory structure like this in your service
directory:

    service_name
    ├── lib 	                # Libraries needed for this service
    └── module                  # Contains the module for the service

In the module directory, at least 1 file should be added: index.php.
This module will handle all the incoming requests. It is a class that should
extend from 'Service_Module'

This is an example of a very basic module:

    <?php
    /**
     * Dummy module
     *
     * @author Christophe Gosiau <christophe@tigron.be>
     * @author Gerry Demaret <gerry@tigron.be>
     */

    class Web_Module_Index extends Service_Module {

        /**
         * Handle call1
         *
         * @access public
         * @param array $data
         */
        public function handle_call1($data) {
        }
    }

After deployment, the remote server can respond to call1. To make a call, use
this:

    $container = \Skeleton\Container\Control\Container::get_by_name('my_remote_container');
    $service = \Skeleton\Container\Control\Service::get_by_name('dummy');
    $container_service = $container->get_container_service($service);
    $data = [
        'param1' => 'this is a test',
        'param2' => 'This is another test'
    ];
    $container_service->call1($data);

A service by default runs in 'production' mode. This means the calls will be
handled by Web_Module_Index.
For testing purpose, the environment can be modified to 'debug'. By doing so
the calls will be handled by Web_Module_Debug. This is optional but strongly
encouraged.
When creating the service, make sure both production calls and debug calls
are available.
To enable debug mode, perform the following calls:

    $container = \Skeleton\Container\Control\Container::get_by_name('my_remote_container');
    $service = \Skeleton\Container\Control\Service::get_by_name('dummy');
    $container_service = $container->get_container_service($service);
    $container_service->set_environment('debug');
    $data = [
        'param1' => 'this is a test',
        'param2' => 'This is another test'
    ];
    $container_service->call1($data);
