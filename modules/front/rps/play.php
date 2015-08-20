<?php


namespace IPS\rps\modules\front\rps;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * play
 */
class _play extends \IPS\Dispatcher\Controller
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		/* We need FontAwesome 4.4+ for the Roshambo icons */
		\IPS\Output::i()->cssFiles = array_merge( \IPS\Output::i()->cssFiles, array(
			\IPS\Http\Url::external( 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' )
		) );

		parent::execute();
	}

	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
		return \IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'rps' )->intro();
	}

	public function play()
	{
		return \IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'rps' )->play();
	}
}