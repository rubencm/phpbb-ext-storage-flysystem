<?php

namespace rubencm\storage_flysystem\adapter;

use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;
use phpbb\storage\adapter\adapter;
use phpbb\storage\adapter\adapter_interface;
use phpbb\storage\stream_interface;
use phpbb\cache\driver\driver_interface;

class dropbox extends adapter implements stream_interface
{
	/**
	 * Cache driver
	 * @var \phpbb\cache\driver\driver_interface
	 */
	protected $cache;

	/** @var DropboxClient */
	protected $client;

	/** @var DropboxAdapter */
	protected $adapter;

	/** @var flysystem */
	protected $filesystem;

	/** @var string */
	protected $path;

	/** @var bool */
	protected $hotlink;

	/** @var bool */
	protected $share;

	/**
	* Constructor
	*
	* @param cache			$cache					Cache object
	* @access public
	*/
	public function __construct(driver_interface $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configure($options)
	{
		$this->client = new DropboxClient($options['access_token']);
		$this->adapter = new DropboxAdapter($this->client);

		$this->filesystem =  new flysystem($this->adapter);
		$this->path = $options['path'];

		if (strlen($this->path) && substr($this->path, -1) != '/')
		{
			$this->path .= '/';
		}

		$this->hotlink = $options['hotlink'];
		$this->share = $options['share'];
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
		return $this->filesystem->read_stream($this->path . $path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function write_stream($path, $resource)
	{
		$this->filesystem->write_stream($this->path . $path, $resource);
	}

	public function file_mimetype($path)
	{
		return $this->filesystem->file_mimetype($this->path . $path);
	}

	public function file_size($path)
	{
		return  $this->filesystem->file_size($this->path . $path);
	}

	protected function normalizePath(string $path): string
	{
		if (preg_match("/^id:.*|^rev:.*|^(ns:[0-9]+(\/.*)?)/", $path) === 1) {
			return $path;
		}
		$path = trim($path, '/');
		return ($path === '') ? '' : '/'.$path;
	}

	public function get_link($path)
	{
		if ($this->share)
		{
			$link = $this->cache->get('_dropbox_sharedlink_' . $this->storage . '_' . $path);

			if ($link === false)
			{
				$parameters = [
					'path' => $this->normalizePath($this->path . $path),
				];

				$link = $this->client->rpcEndpointRequest('sharing/list_shared_links', $parameters);

				if (isset($link['links'][0]))
				{
					$link = $link['links'][0]['url'];
				}
				else
				{
					// If there are not links, generate another
					$link = $this->client->rpcEndpointRequest('sharing/create_shared_link_with_settings', $parameters)['url'];
				}

				$this->cache->put('_dropbox_sharedlink_' . $this->storage . '_' . $path, $link);
			}
		}
		else if ($this->hotlink)
		{
			$link = $this->cache->get('_dropbox_temporarylink_' . $this->storage . '_' . $path);

			if ($link === false)
			{
				$link = $this->adapter->getTemporaryLink($this->path . $path);

				// Temporary links expire in four hours
				$this->cache->put('_dropbox_temporarylink_' . $this->storage . '_' . $path, $link, 4*3600);
			}
		}

		return $link;
	}

	public function free_space()
	{
		$space_usage = $link = $this->client->rpcEndpointRequest('users/get_space_usage');

		return (int) ($space_usage['allocation']['allocated'] - $space_usage['used']);
	}
}
