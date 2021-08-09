<?php

namespace rubencm\storage_flysystem\provider;

use \phpbb\storage\provider\provider_interface;

class dropbox implements provider_interface
{
	/**
	 * {@inheritdoc}
	 */
	public function get_name()
	{
		return 'dropbox';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_adapter_class(): string
	{
		return \rubencm\storage_flysystem\adapter\dropbox::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options()
	{
		return [
			'access_token' => array('type' => 'password'),
			'path' => ['type' => 'text'],
			'hotlink' => [
				'type' => 'radio',
				'options' => [
						'YES' => '1',
						'NO' => '0',
				],
			],
			'share' => [
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
		return version_compare(PHP_VERSION, '7.0.0', '>=');
	}
}
