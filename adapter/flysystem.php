<?php
namespace rubencm\storage_flysystem\adapter;

use League\Flysystem\AdapterInterface;
use phpbb\storage\adapter\adapter;
use phpbb\storage\stream_interface;
use phpbb\storage\exception\exception;
use League\Flysystem\Util;

class flysystem
{
	/** @var AdapterInterface */
	protected $adapter;

	/** @var League\Flysystem\Config; */
	protected $config;

	/**
	 * {@inheritdoc}
	 */
	public function __construct(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
		$this->config = Util::EnsureConfig([
			'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function put_contents($path, $content)
	{
		try
		{
			if(!$this->adapter->write($path, $content, $this->config))
			{
				throw new exception('CANNOT_DUMP_FILE', $path);
			}
		}
		catch (\League\Flysystem\FileExistsException $e)
		{
			throw new exception('CANNOT_OPEN_FILE', $path, array(), $e);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_contents($path)
	{
		try
		{
			$content = $this->adapter->read($path);

			if (!$content)
			{
				throw new exception('CANNOT_READ_FILE', $path);
			}
		}
		catch (\League\Flysystem\FileNotFoundException $e)
		{
			throw new exception('CANNOT_OPEN_FILE', $path, array(), $e);
		}

		return $content['contents'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists($path)
	{
		return $this->adapter->has($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete($path)
	{
		try
		{
			if(!$this->adapter->delete($path))
			{
				throw new exception('CANNOT_DELETE_FILES', $path_orig);
			}
		}
		catch (\League\Flysystem\FileNotFoundException $e)
		{
			throw new exception('CANNOT_DELETE_FILES', $path_orig, array(), $e);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function rename($path_orig, $path_dest)
	{
		try
		{
			if(!$this->adapter->rename($path_orig, $path_dest))
			{
				throw new exception('CANNOT_RENAME_FILE', $path_orig);
			}
		}
		catch (\Exception $e)
		{
			throw new exception('CANNOT_RENAME_FILE', $path_orig, array(), $e);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function copy($path_orig, $path_dest)
	{
		try
		{
			if(!$this->adapter->copy($path_orig, $path_dest))
			{
				throw new exception('CANNOT_COPY_FILE', $path_orig);
			}
		}
		catch (\Exception $e)
		{
			throw new exception('CANNOT_COPY_FILE', $path_orig, array(), $e);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function read_stream($path)
	{
		return $this->adapter->readStream($path)['stream'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function write_stream($path, $resource)
	{
		$this->adapter->writeStream($path, $resource, $this->config);
	}

	public function file_mimetype($path)
	{
		try
		{
			$mimetype = $this->adapter->getMimeType($path);
		}
		catch (\Exception $e)
		{
			$extension_guesser = new \phpbb\mimetype\extension_guesser();
			$mimetype = $extension_guesser->guess($path);
		}

		return ['mimetype' => $mimetype];
	}

	public function file_size($path)
	{
		return ['size' => $this->adapter->getSize($path)['size']];
	}

	public function get_link($path)
	{
		return $this->adapter->get_link($path);
	}

	public function free_space()
	{
		return $this->adapter->free_space();
	}
}
