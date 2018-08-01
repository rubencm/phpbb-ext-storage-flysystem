<?php

namespace rubencm\storage_flysystem\provider;

use \phpbb\storage\provider\provider_interface;

class openstack_swift implements provider_interface
{
	/**
	 * {@inheritdoc}
	 */
	public function get_name()
	{
		return 'openstack_swift';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_adapter_class()
	{
		return \rubencm\storage_flysystem\adapter\openstack_swift::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options()
	{
		return [
			'auth_url' => ['type' => 'text'],
			'region' => ['type' => 'text'],
			'user_id' => ['type' => 'text'],
			'password' => ['type' => 'password'],
			'project_id' => ['type' => 'text'],
			'container_name' => ['type' => 'text'],
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
