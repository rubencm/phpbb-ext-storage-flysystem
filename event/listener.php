<?php

namespace rubencm\storage_flysystem\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use rubencm\storage_flysystem\adapter\dropbox;
use rubencm\storage_flysystem\adapter\aws_s3;
use phpbb\storage\storage;
use phpbb\language\language;
use phpbb\config\config;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var language */
	protected $lang;

	/** @var storage */
	protected $storage_attachment;

	/** @var storage */
	protected $storage_avatar;

	/**
	* Constructor
	*
	* @param config			$config					Config object
	* @param language		$lang					Language object
	* @param storage		$storage_attachment		Storage attachment
	* @param storage		$storage_avatar			Storage avatar
	* @access public
	*/
	public function __construct(config $config, language $lang, storage $storage_attachment, storage $storage_avatar)
	{
		$this->config = $config;
		$this->lang = $lang;
		$this->storage_attachment = $storage_attachment;
		$this->storage_avatar = $storage_avatar;
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
			'core.acp_storage_load'	=> 'add_lang',
			'core.download_file_send_to_browser_before'	=> 'attachment_redirect',
			'core.get_avatar_after'	=> 'add_avatar_hotlink',
			'core.viewtopic_modify_post_row' => 'attachment_new_window'
		);
	}

	/**
	* Add language strings
	*
	* @param \phpbb\event\data $event The event object
	* @return void
	* @access public
	*/
	public function add_lang($event)
	{
		$this->lang->add_lang('storage_acp', 'rubencm/storage_flysystem');
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
		if (!$event['html'] || !$this->storage_option('avatar', 'hotlink'))
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

		$src = $this->storage_avatar->get_link($this->avatar_real_file($event['row']['avatar']));

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

	protected function storage_option($storage, $option)
	{
		if (!isset($this->config["storage\\$storage\\config\\$option"]))
		{
			return false;
		}

		return $this->config["storage\\$storage\\config\\$option"];
	}

	/**
	* Download avatar with direct link
	*
	* @param \phpbb\event\data $event The event object
	* @return void
	* @access public
	*/
	public function attachment_redirect($event)
	{
		if (!($this->storage_option('attachment', 'hotlink') || $this->storage_option('attachment', 'share')))
		{
			return;
		}

		$event['redirect'] = $this->storage_attachment->get_link($event['attachment']['physical_filename']);
	}

	/**
	* Modify the html to open external links in a new window
	*
	* @param \phpbb\event\data $event The event object
	* @return void
	* @access public
	*/
	public function attachment_new_window($event)
	{
		if ($this->storage_option('attachment', 'share'))
		{
			if (!empty($event['attachments'][$event['row']['post_id']]))
			{
				for ($i=0; $i < count($event['attachments'][$event['row']['post_id']]); $i++)
				{
					$dom = new \DOMDocument();
					$dom->loadHTML($event['attachments'][$event['row']['post_id']][$i]);
					$xpath = new \DOMXPath($dom);

					$link = $xpath->query('//a');

					if (!$link)
					{
						return;
					}

					$link->item(0)->setAttribute('target', '_blank');

					$data = $event->get_data();
					$data['attachments'][$event['row']['post_id']][$i] = $dom->saveHTML();
					$event->set_data($data);
				}
			}
		}
	}
}
