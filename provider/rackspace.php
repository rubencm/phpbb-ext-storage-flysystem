<?php

namespace rubencm\storage_flysystem\provider;

use \phpbb\storage\provider\provider_interface;

class rackspace implements provider_interface
{
	/**
	 * {@inheritdoc}
	 */
	public function get_name()
	{
		return 'rackspace';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_adapter_class()
	{
		return \rubencm\storage_flysystem\adapter\rackspace::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options()
	{
		return [
			'username' => ['type' => 'text'],
			'password' => ['type' => 'password'],
			'path' => ['type' => 'text'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available()
	{
		return true;
	}
}
