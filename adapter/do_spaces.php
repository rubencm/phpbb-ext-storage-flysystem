<?php

namespace rubencm\storage_flysystem\adapter;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use phpbb\storage\adapter\adapter_interface;

class do_spaces implements adapter_interface
{
	/** @var flysystem */
	protected $filesystem;

	/** @var S3Client */
	protected $client;

	/** @var string */
	protected $bucket;

	/** @var string */
	protected $path;

	/**
	 * {@inheritdoc}
	 */
	public function configure($options)
	{
		$this->client = new S3Client([
			'credentials' => [
				'key' => $options['key'],
				'secret' => $options['secret'],
			],
			'region' => $options['region'],
			'version' => $options['version'],
			'endpoint' => 'https://' . $options['region'] . '.digitaloceanspaces.com',
		]);

		$adapter = new AwsS3Adapter($this->client, $options['bucket']);

		$this->filesystem =  new flysystem($adapter);
		$this->bucket = $options['bucket'];
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

	public function generate_link($path)
	{
		return $this->client->getObjectUrl($this->bucket, $this->path . $path);
		//return '//' . $this->bucket . '.s3.amazonaws.com/' . $this->path . $path
	}
}
