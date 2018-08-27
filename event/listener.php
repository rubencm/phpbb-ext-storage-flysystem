<?php

namespace rubencm\storage_flysystem\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\language\language;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var language */
	protected $lang;

	/**
	* Constructor
	*
	* @param language		$lang					Language object
	* @access public
	*/
	public function __construct(language $lang)
	{
		$this->lang = $lang;
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
}
