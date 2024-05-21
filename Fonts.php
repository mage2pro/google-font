<?php
namespace Dfe\GoogleFont;
use ArrayIterator as AI;
use Df\Core\Exception as DFE;
use Dfe\Google\Settings as S;
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
	 * @used-by \Dfe\GoogleFont\Controller\Index\Preview::contents()
	 * @throws DFE
	 */
	function get(string $f):Font {return df_assert(dfa($this->items(), $f), "Font family is not found: «{$f}».");}

	/**
	 * 2015-11-27 https://php.net/manual/iteratoraggregate.getiterator.php
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 */
	function getIterator():AI {return new AI($this->items());}

	/**
	 * 2015-11-27
	 * @used-by self::count()
	 * @used-by self::get()
	 * @used-by self::getIterator()
	 * @return array(string => Font)
	 */
	private function items():array {return dfc($this, function():array {
		/** @var Font[] $fonts */
		$fonts = array_map(function(array $itemA):Font {return new Font($itemA);}, $this->responseA());
		$families = array_map(function(Font $f):string {return $f->family();}, $fonts); /** @var string[] $families */
		return array_combine($families, $fonts);
	});}

	/**
	 * 2015-11-27
	 * 2022-12-05: We do not need to check that the result is an array: https://3v4l.org/pBUvg
	 * @used-by self::items()
	 * @return array(string => mixed)
	 * @throws ResponseValidator
	 */
	private function responseA():array {return dfc($this, function():array {
		$debug = true; /** @var bool $debug */ /** @var string $k */
		list($url, $query) = $debug || !($k = S::s()->serverApiKey())
			? ['https://mage2.pro/google-fonts.json', []]
			: ['https://www.googleapis.com/webfonts/v1/webfonts', ['key' => $k, 'sort' => 'alpha']]
		;
		$r = df_http_json($url, $query); /** @var array(string => mixed) $r */
		/**
		 * 2015-11-17
		 * В документации об этом ни слова не сказано, однако в случае сбоя Google API возвращает JSON следующией структуры:
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
		if ($e = dfa($r, 'error')) { /** @var array(string => mixed)|null $e */
			throw (new Exception($e))->standard();
		}
		return dfa($r, 'items'); # 2015-11-27 https://developers.google.com/fonts/docs/developer_api#Example
	});}
}