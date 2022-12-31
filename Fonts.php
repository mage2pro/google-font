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
	 */
	function count():int {return count($this->items());}

	/**
	 * 2015-11-29
	 * @throws \Exception
	 */
	function get(string $f):Font {
		if (!($r = dfa($this->items(), $f))) { /** @var Font|null $r */
			throw new \Exception("Font family is not found: «{$f}».");
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
	 * 2022-12-05: We do not need to check that the result is an array: https://3v4l.org/pBUvg
	 * @return array(string => mixed)
	 * @throws \Exception
	 */
	private function responseA():array {return dfc($this, function() {
		$debug = true; /** @var bool $debug */ /** @var string $k */
		list($url, $query) = $debug || !($k = S::s()->serverApiKey())
			? ['https://mage2.pro/google-fonts.json', []]
			: ['https://www.googleapis.com/webfonts/v1/webfonts', ['key' => $k, 'sort' => 'alpha']]
		;
		$r = df_http_json($url, $query); /** @var array(string => mixed) $r */
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
		if ($error = dfa($r, 'error')) { /** @var array(string => mixed)|null $error */
			throw (new Exception($error))->standard();
		}
		return dfa($r, 'items'); # 2015-11-27 https://developers.google.com/fonts/docs/developer_api#Example
	});}
}