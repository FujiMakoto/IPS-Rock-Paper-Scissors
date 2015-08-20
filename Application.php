<?php
/**
 * @brief		Rock Paper Scissors Application Class
 * @author		<a href='https://www.Makoto.io'>Makoto Fujimoto</a>
 * @copyright	(c) 2015 Makoto Fujimoto
 * @package		IPS Social Suite
 * @subpackage	Rock Paper Scissors
 * @since		20 Aug 2015
 * @version		
 */
 
namespace IPS\rps;

/**
 * Rock Paper Scissors Application Class
 */
class _Application extends \IPS\Application
{
	/**
	 * Application icon
	 *
	 * @return	string
	 */
	public function get__icon()
	{
		//return 'hand-scissors-o';  Only available with Font Awesome 4.4
		return 'scissors';
	}
}