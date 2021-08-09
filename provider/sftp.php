<?php

namespace rubencm\storage_flysystem\provider;

use \phpbb\storage\provider\provider_interface;

class sftp implements provider_interface
{
	/**
	 * {@inheritdoc}
	 */
	public function get_name()
	{
		return 'sftp';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_adapter_class(): string
	{
		return \rubencm\storage_flysystem\adapter\sftp::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options()
	{
		return [
			'host' => ['type' => 'text'],
			'username' => ['type' => 'text'],
			'password' => ['type' => 'password'],
			'port' => ['type' => 'text'],
			'path' => ['type' => 'text'],
			'private_key' => ['type' => 'text'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available()
	{
		return function_exists('ftp_connect');
	}
}
