<?php

namespace rubencm\storage_flysystem\adapter;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use phpbb\storage\adapter\adapter_interface;

class openstack_swift implements adapter_interface
{
	/** @var flysystem */
	protected $filesystem;

	/** @var string */
	protected $path;

	/**
	 * {@inheritdoc}
	 */
	public function configure($options)
	{
		$openstack = new OpenStack\OpenStack([
		    'authUrl' => $options['auth_url'],
		    'region'  => $options['region'],
		    'user'    => [
		        'id'       => $options['user_id'],
		        'password' => $options['password'],
		    ],
		    'scope'   => ['project' => ['id' => $options['project_id']]]
		]);

		$container = $openstack->objectStoreV1()
			->getContainer($options['container_name']);

		$adapter = new Nimbusoft\Flysystem\OpenStack\SwiftAdapter($container);

		$this->filesystem = flysystem($adapter);

		$this->path = $options['path'];

		if (strlen($this->path) && substr($this->path, -1) != '/')
		{
			$this->path .= '/';
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function put_contents($path, $content)
	{
		$this->filesystem->put_contents($this->path . $path, $content);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_contents($path)
	{
		return $this->filesystem->get_contents($this->path . $path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists($path)
	{
		return $this->filesystem->exists($this->path . $path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete($path)
	{
		$this->filesystem->delete($this->path . $path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rename($path_orig, $path_dest)
	{
		$this->filesystem->rename($this->path . $path_orig, $this->path . $path_dest);
	}

	/**
	 * {@inheritdoc}
	 */
	public function copy($path_orig, $path_dest)
	{
		$this->filesystem->copy($this->path . $path_orig, $this->path . $path_dest);
	}

	/**
	 * {@inheritdoc}
	 */
	public function read_stream($path)
	{
		return $this->filesystem->readStream($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function write_stream($path, $resource)
	{
		$this->filesystem->writeStream($path, $resource);
	}

	public function file_mimetype($path)
	{
		return $mimetype = $this->filesystem->file_mimetype($path);
	}

	public function file_size($path)
	{
		return  $this->filesystem->file_size($path);
	}
}
