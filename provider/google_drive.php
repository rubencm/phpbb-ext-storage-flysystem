<?php

namespace rubencm\storage_flysystem\provider;

use \phpbb\storage\provider\provider_interface;

class google_drive implements provider_interface
{
	/**
	 * {@inheritdoc}
	 */
	public function get_name()
	{
		return 'google_drive';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_adapter_class(): string
	{
		return \rubencm\storage_flysystem\adapter\google_drive::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options()
	{
		return [
			'app_client_id' => ['type' => 'text'],
			'client_secret' => ['type' => 'password'],
			'refresh_token' => ['type' => 'text'],
			'root' => ['type' => 'text'],
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
