# skeleton-container

## Description

This library enables the communication with a remote server. The remote server
needs to have the skeleton-remote package enabled

## Installation

Installation via composer:

    composer require tigron/skeleton-container

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
