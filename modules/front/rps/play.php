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
	const ROCK		= 'rock';
	const PAPER 	= 'paper';
	const SCISSORS	= 'scissors';

	private function _cheater()
	{
		return ( in_array( 'rps_cheater', \IPS\Request::i()->cookie )
			and \IPS\Request::i()->cookie['rps_cheater'] == 'RWwgUHN5IENvbmdyb28=' );
	}

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
		\IPS\Output::i()->cssFiles = array_merge( \IPS\Output::i()->cssFiles, \IPS\Theme::i()->css( 'play.css' ) );

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
		return \IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'rps' )->select();
	}

	/**
	 * Rock, paper, scissors shoot!
	 *
	 * @return void
	 */
	public function shoot()
	{
		/* Key beats value */
		$validMoves = array(
			static::ROCK => static::SCISSORS,
			static::PAPER => static::ROCK,
			static::SCISSORS => static::PAPER
		);
		$playerMove = \IPS\Request::i()->move;
		$compMove   = array_rand( $validMoves );

		/* Make sure we actually received a valid move */
		if ( !in_array($playerMove, $validMoves) ) {
			return \IPS\Output::i()->error( 'generic_error', 'INVALID_MOVE', 400 );
		}

		/* Did we win? */
		$winner = $winner = $chickenDinner = ( ( $playerMove !== $validMoves[ $compMove ] ) or $this->_cheater() );
		$draw = ( $playerMove === $compMove );

		/* Update the scoreboard */
		$scoreboard = in_array( 'rps_scoreboard', \IPS\Request::i()->cookie )
			? json_decode( \IPS\Request::i()->cookie['rps_scoreboard'] )
			: array( 'wins' => 0, 'loses' => 0, 'draws' => 0 );

		if ( $draw ) {
			$scoreboard['draws']++;
		} elseif ( $winner ) {
			$scoreboard['wins']++;
		} else {
			$scoreboard['loses']++;
		}

		\IPS\Request::i()->setCookie( 'rps_scoreboard', json_encode( $scoreboard ) );

		return \IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'rps' )->result( $winner, $draw, array(
			'player' => $playerMove, 'computer' => $compMove
		) );
	}

	/**
	 * @link https://www.youtube.com/watch?v=MjmnknmC8H0
	 *
	 * @return	void
	 */
	public function konami()
	{
		if ( \IPS\Request::i()->code != 'RWwgUHN5IENvbmdyb28' ) {
			return \IPS\Output::i()->error( 'page_not_found', '2S100/5', 404 );
		}
		\IPS\Session::i()->csrfCheck();

		/* ^('-')^ ^('-')^ v('-')v v('-')v <('-'<) (>'-')> <('-'<) (>'-')> B A */
		if ( in_array( 'rps_cheater', \IPS\Request::i()->cookie ) )
		{
			$message = 'Cheat mode disabled!';
			unset( \IPS\Request::i()->cookie['rps_cheater'] );
		}
		else
		{
			$message = 'Cheat mode enabled!';
			\IPS\Request::i()->setCookie( 'rps_cheater', 'RWwgUHN5IENvbmdyb28' );
		}

		if ( \IPS\Request::i()->isAjax() ) {
			return \IPS\Output::i()->json( $message );
		}
		return \IPS\Output::i()->redirect( 'app=rps&module=rps&controller=play', $message, 302 );
	}
}