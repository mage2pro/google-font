<?php
namespace Df\GoogleFont;
use ArrayIterator as AI;
use Df\Google\Settings as S;
/** @method static Fonts s() */
final class Fonts extends \Df\Core\O implements \Countable, \IteratorAggregate {
	/**
	 * 2015-11-27
	 * @override
	 * @see \Countable::count()
	 * @return int
	 */
	function count() {return count($this->items());}

	/**
	 * 2015-11-29
	 * @param string $family
	 * @return Font
	 * @throws \Exception
	 */
	function get($family) {
		if (!($r = dfa($this->items(), $family))) { /** @var Font|null $r */
			throw new \Exception("Font family is not found: «{$family}».");
		}
		return $r;
	}

	/**
	 * 2015-11-27
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * https://www.php.net/manual/iteratoraggregate.getiterator.php
	 * @return AI
	 */
	function getIterator() {return new AI($this->items());}

	/**
	 * 2015-11-27
	 * @return array(string => Font)
	 */
	private function items() {return dfc($this, function() {
		/** @var mixed $result */ /** @var Font[] $fonts */
		$fonts = array_map(function(array $itemA) {return new Font($itemA);}, $this->responseA());
		$families = array_map(function(Font $font) {return $font->family();}, $fonts); /** @var string[] $families */
		return array_combine($families, $fonts);
	});}

	/**
	 * 2015-11-27
	 * @return array(string => mixed)
	 * @throws \Exception
	 */
	private function responseA() {return dfc($this, function() {
		$debug = true; /** @var bool $debug */
		$result = df_json_decode(
			$debug || !S::s()->serverApiKey()
			? df_http_get('https://mage2.pro/google-fonts.json')
			: df_http_get('https://www.googleapis.com/webfonts/v1/webfonts', [
				'key' => S::s()->serverApiKey(), 'sort' => 'alpha'
			])
		); /** @var array(string => mixed) $result */
		/**
		 * 2015-11-17
		 * В документации об этом ни слова не сказано,
		 * однако в случае сбоя Google API возвращает JSON следующией структуры:
		 *	{
		 *		error: {
		 *			errors: [
		 *				{
		 *					domain: "usageLimits",
		 *					reason: "accessNotConfigured",
		 *					message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration.",
		 *					extendedHelp: "https://console.developers.google.com"
		 *				}
		 *			],
		 *			code: 403,
		 *			message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration."
		 *		}
		 *	}
		 * https://developers.google.com/fonts/docs/developer_api
		 */
		if ($error = dfa($result, 'error')) { /** @var array(string => mixed)|null $error */
			throw (new Exception($error))->standard();
		}
		# 2015-11-27 https://developers.google.com/fonts/docs/developer_api#Example
		return df_result_array(dfa($result, 'items'));
	});}
}