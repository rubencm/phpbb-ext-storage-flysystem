<?php

namespace rubencm\storage_flysystem\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\storage\storage;
use phpbb\config\config;
use phpbb\language\language;

/**
* Event listener
*/
class attachment_listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var language */
	protected $lang;

	/** @var storage */
	protected $storage;

	/**
	* Constructor
	*
	* @param config			$config			Config object
	* @param language		$lang					Language object
	* @param storage		$storage		Storage attachment
	* @access public
	*/
	public function __construct(config $config, language $lang, storage $storage)
	{
		$this->config = $config;
		$this->lang = $lang;
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
			'core.parse_attachments_modify_template_data'	=> 'images_as_link',
			'core.download_file_send_to_browser_before'	=> 'attachment_redirect',
			'core.viewtopic_modify_post_row' => 'attachment_new_window'
		);
	}

	/**
	* Force image attachments to be shown as link
	*
	* @param \phpbb\event\data $event The event object
	* @return void
	* @access public
	*/
	public function images_as_link($event)
	{
		if ($this->storage_option('share'))
		{
			$data = $event->get_data();

			if (!isset($data['block_array']['S_FILE']))
			{
				array_pop($data['update_count']);
			}

			$data['block_array']['S_IMAGE'] = false;
			$data['block_array']['S_THUMBNAIL'] = false;
			$data['block_array']['S_FILE'] = true;
			$data['block_array']['L_DOWNLOAD_COUNT'] = $this->lang->lang('DOWNLOAD_COUNTS', (int) $event['attachment']['download_count']);

			$event->set_data($data);
		}
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
		if ($this->storage_option('hotlink') || $this->storage_option('share'))
		{
			$url =  $this->storage->get_link($event['attachment']['physical_filename']);

			if ($url)
			{
				$event['redirect'] = $url;
			}
		}
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
		if ($this->storage_option('share'))
		{
			if (!empty($event['attachments'][$event['row']['post_id']]))
			{
				for ($i=0; $i < count($event['attachments'][$event['row']['post_id']]); $i++)
				{
					$dom = new \DOMDocument();
					$dom->loadHTML($event['attachments'][$event['row']['post_id']][$i]);
					$xpath = new \DOMXPath($dom);

					$link = $xpath->query('//a');

					if (!$link || !$link['length'])
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

	protected function storage_option($option)
	{
		if (!isset($this->config["storage\\attachment\\config\\$option"]))
		{
			return false;
		}

		return $this->config["storage\\attachment\\config\\$option"];
	}
}
