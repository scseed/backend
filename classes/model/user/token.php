<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * User_token Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Model_User_Token extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('user_tokens')
			->fields(array(
				'id' => Jelly::field('Primary'),
				'user' => Jelly::field('BelongsTo'),
				'user_agent' => Jelly::field('String', array(
					'default' => sha1(Request::$user_agent),
				)),
				'token' => Jelly::field('String', array(
					'default' => self::create_token(),
				)),
				'created' => Jelly::field('Timestamp', array(
					'default' => time(),
				)),
				'expires' => Jelly::field('Timestamp'),
			));
	}

	/**
	 * Finds a new unique token, using a loop to make sure that the token does
	 * not already exist in the database. This could potentially become an
	 * infinite loop, but the chances of that happening are very unlikely.
	 *
	 * @return  string
	 */
	protected static function create_token()
	{
		while (TRUE)
		{
			// Create a random token
			$token = Text::random('alnum', 32);

			// Make sure the token does not already exist
			$count = Jelly::query('user_token')
				->where('token', '=', $token)
				->count();

			if ($count === 0)
			{
				// A unique token has been found
				return $token;
			}
		}
	}
} // End Model_User_token