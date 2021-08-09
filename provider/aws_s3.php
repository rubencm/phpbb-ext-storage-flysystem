<?php

namespace rubencm\storage_flysystem\provider;

use \phpbb\storage\provider\provider_interface;

class aws_s3 implements provider_interface
{
	/**
	 * {@inheritdoc}
	 */
	public function get_name()
	{
		return 'aws_s3';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_adapter_class(): string
	{
		return \rubencm\storage_flysystem\adapter\aws_s3::class;
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
