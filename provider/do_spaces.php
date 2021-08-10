<?php

namespace rubencm\storage_flysystem\provider;

use \phpbb\storage\provider\provider_interface;

class do_spaces implements provider_interface
{
	/**
	 * {@inheritdoc}
	 */
	public function get_name()
	{
		return 'do_spaces';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_adapter_class(): string
	{
		return \rubencm\storage_flysystem\adapter\do_spaces::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options()
	{
		return [
			'key' => ['type' => 'text'],
			'secret' => ['type' => 'password'],
			'region' => ['type' => 'text'],
			'version' => ['type' => 'text'],
			'bucket' => ['type' => 'text'],
			'path' => ['type' => 'text'],
			'hotlink' => [
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
		return true;
	}
}
