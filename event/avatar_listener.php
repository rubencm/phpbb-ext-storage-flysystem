<?php

namespace rubencm\storage_flysystem\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\storage\storage;
use phpbb\config\config;

/**
* Event listener
*/
class avatar_listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var storage */
	protected $storage;

	/**
	* Constructor
	*
	* @param config			$config					Config object
	* @param storage		$storage				Storage avatar
	* @access public
	*/
	public function __construct(config $config,  storage $storage)
	{
		$this->config = $config;
		$this->storage = $storage;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.get_avatar_after'	=> 'add_avatar_hotlink',
		);
	}

	/**
	* Modify the html to link avatar directly
	*
	* @param \phpbb\event\data $event The event object
	* @return void
	* @access public
	*/
	public function add_avatar_hotlink($event)
	{
		if (!$event['html'] || !$this->storage_option('hotlink'))
		{
			return;
		}

		$dom = new \DOMDocument();
		$dom->loadHTML($event['html']);
		$xpath = new \DOMXPath($dom);

		$img = $xpath->query('//img[@src]');
		$src = $img->item(0)->getAttribute('src');

		if (!$src)
		{
			return;
		}

		$src = $this->storage->get_link($this->avatar_real_file($event['row']['avatar']));

		$img->item(0)->setAttribute('src', $src);
		$event['html'] = $dom->saveHTML();
	}

	/**
	* Get the avatar file
	*
	* @param string $filename File name
	* @return string
	* @access protected
	*/
	protected function avatar_real_file($filename)
	{
		$avatar_group = false;

		if (isset($filename[0]) && $filename[0] === 'g')
		{
			$avatar_group = true;
			$filename = substr($filename, 1);
		}

		$ext = substr(strrchr($filename, '.'), 1);
		$filename = ($avatar_group ? 'g' : '') . (int) $filename . '.' . $ext;

		return $this->config['avatar_salt'] . '_' . $filename;
	}

	protected function storage_option($option)
	{
		if (!isset($this->config["storage\\avatar\\config\\$option"]))
		{
			return false;
		}

		return $this->config["storage\\avatar\\config\\$option"];
	}
}
