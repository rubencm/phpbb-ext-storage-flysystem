<?php

namespace rubencm\storage_flysystem\adapter;

use OpenCloud\OpenStack as OpenCloud_OpenStack;
use OpenCloud\Rackspace as OpenCloud_Rackspace;
use League\Flysystem\Rackspace\RackspaceAdapter as Adapter;
use phpbb\storage\adapter\adapter_interface;

class rackspace implements adapter_interface
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
		$client = new Rackspace(Rackspace::UK_IDENTITY_ENDPOINT, array(
			'username' => $options['username'],
			'apiKey' => $options['password'],
		));

		$store = $client->objectStoreService('cloudFiles', 'LON');
		$container = $store->getContainer('flysystem');

		$adapter = new Adapter($container);
		$this->filesystem = new flysytem($adapter);

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
