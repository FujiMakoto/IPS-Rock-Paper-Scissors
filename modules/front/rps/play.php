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

	/**
	 * @brief	Base URL
	 */
	protected static $baseUrl = 'app=rps&module=rps&controller=play';

	private function _cheater()
	{
		return ( !empty( \IPS\Request::i()->cookie['rps_cheater'] )
			and \IPS\Request::i()->cookie['rps_cheater'] == 'RWwgUHN5IENvbmdyb28=' );
	}

	/**
	 * Get the scoreboard
	 *
	 * @return array
	 */
	protected function _scoreboard()
	{
		if ( !empty( \IPS\Request::i()->cookie['rps_scoreboard'] ) )
		{
			$scoreboard = json_decode( \IPS\Request::i()->cookie['rps_scoreboard'], true );
			if ( isset( $scoreboard['wins'], $scoreboard['losses'], $scoreboard['draws'] ) ) {
				return $scoreboard;
			}
		}

		return array( 'wins' => 0, 'losses' => 0, 'draws' => 0 );
	}

	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack( 'module__rps_rps' );

		/* We need FontAwesome 4.4+ for the Roshambo icons */
		\IPS\Output::i()->cssFiles = array_merge( \IPS\Output::i()->cssFiles, array(
			\IPS\Http\Url::external( 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' )
		) );
		\IPS\Output::i()->cssFiles = array_merge( \IPS\Output::i()->cssFiles, \IPS\Theme::i()->css( 'play.css' ) );
		\IPS\Output::i()->jsFiles = array_merge( \IPS\Output::i()->jsFiles, \IPS\Output::i()->js( 'front_play.js', 'rps' ) );

		parent::execute();
	}

	/**
	 * Game start!
	 *
	 * @return	void
	 */
	protected function manage()
	{
		return \IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'rps' )->select( $this->_scoreboard() );
	}

	/**
	 * Rock, paper, scissors shoot!
	 *
	 * @return void
	 */
	public function shoot()
	{
		\IPS\Session::i()->csrfCheck();

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
		$draw = ( ( !$this->_cheater() ) and ( $playerMove === $compMove ) );

		/* Update the scoreboard */
		$scoreboard = $this->_scoreboard();

		if ( $draw ) {
			$scoreboard['draws']++;
		} elseif ( $winner ) {
			$scoreboard['wins']++;
		} else {
			$scoreboard['losses']++;
		}

		\IPS\Request::i()->setCookie( 'rps_scoreboard', json_encode( $scoreboard ) );

		return \IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'rps' )->result( $winner, $draw, array(
			'player' => $playerMove, 'computer' => $compMove
		), $scoreboard );
	}

	/**
	 * Reset the scoreboard
	 */
	public function resetScore()
	{
		\IPS\Session::i()->csrfCheck();

		\IPS\Request::i()->setCookie( 'rps_scoreboard', json_encode(
			array( 'wins' => 0, 'losses' => 0, 'draws' => 0 )
		) );

		if ( \IPS\Request::i()->isAjax() ) {
			return \IPS\Output::i()->json( 'rps_score_reset' );
		}
		return \IPS\Output::i()->redirect( \IPS\Http\Url::internal( static::$baseUrl ), 'rps_score_reset', 302 );
	}

	/**
	 * @link https://www.youtube.com/watch?v=MjmnknmC8H0
	 *
	 * @return	void
	 */
	public function konami()
	{
		if ( \IPS\Request::i()->code != 'RWwgUHN5IENvbmdyb28=' ) {
			return \IPS\Output::i()->error( 'page_not_found', '2S100/5', 404 );
		}
		\IPS\Session::i()->csrfCheck();

		/* ^('-')^ ^('-')^ v('-')v v('-')v <('-'<) (>'-')> <('-'<) (>'-')> B A */
		if ( isset( \IPS\Request::i()->cookie['rps_cheater'] ) )
		{
			$message = 'Cheat mode disabled!';
			\IPS\Request::i()->setCookie( 'rps_cheater', null );
		}
		else
		{
			$message = 'Cheat mode enabled!';
			\IPS\Request::i()->setCookie( 'rps_cheater', 'RWwgUHN5IENvbmdyb28=' );
		}

		if ( \IPS\Request::i()->isAjax() ) {
			return \IPS\Output::i()->json( $message );
		}
		return \IPS\Output::i()->redirect( \IPS\Http\Url::internal( static::$baseUrl ), $message, 302 );
	}
}