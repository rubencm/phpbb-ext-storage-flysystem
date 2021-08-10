<?php

namespace rubencm\storage_flysystem\provider;

use \phpbb\storage\provider\provider_interface;

class ftp implements provider_interface
{
	/**
	 * {@inheritdoc}
	 */
	public function get_name()
	{
		return 'ftp';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_adapter_class(): string
	{
		return \rubencm\storage_flysystem\adapter\ftp::class;
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
			'passive' => [
				'type' => 'radio',
				'options' => [
						'YES' => '1',
						'NO' => '0',
				],
			],
			'ssl' => [
				'type' => 'radio',
				'options' => [
						'YES' => '1',
						'NO' => '0',
				],
			],
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
