<?php

namespace rubencm\storage_flysystem\adapter;

use League\Flysystem\Sftp\SftpAdapter;
use phpbb\storage\adapter\adapter;
use phpbb\storage\stream_interface;

class ftp extends adapter implements stream_interface
{
	/** @var flysystem */
	protected $filesystem;

	/**
	 * {@inheritdoc}
	 */
	public function configure($options)
	{
		$adapter = new SftpAdapter([
			'host' => $options['host'],
			'username' => $options['username'],
			'password' => $options['password'],
			'port' => $options['port'],
			'root' => $options['path'],
			'timeout' => 10,
			'private_key' => $options['private_key'],
		]);

		$this->filesystem =  new flysystem($adapter);
	}

	/**
	 * {@inheritdoc}
	 */
	public function put_contents($path, $content)
	{
		$this->filesystem->put_contents($path, $content);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_contents($path)
	{
		return $this->filesystem->get_contents($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists($path)
	{
		return $this->filesystem->exists($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete($path)
	{
		$this->filesystem->delete($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rename($path_orig, $path_dest)
	{
		$this->filesystem->rename($path_orig, $path_dest);
	}

	/**
	 * {@inheritdoc}
	 */
	public function copy($path_orig, $path_dest)
	{
		$this->filesystem->copy($path_orig, $path_dest);
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
