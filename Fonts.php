<?php
namespace Df\GoogleFont;
use Df\Google\Settings as S;
/** @method static Fonts s() */
class Fonts extends \Df\Core\O implements \IteratorAggregate, \Countable {
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
		/** @var Font|null $result */
		$result = dfa($this->items(), $family);
		if (!$result) {
			throw new \Exception("Font family is not found: «{$family}».");
		}
		return $result;
	}

	/**
	 * 2015-11-27
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * @return \Traversable
	 */
	function getIterator() {return new \ArrayIterator($this->items());}

	/**
	 * 2015-11-27
	 * @override
	 * @see \Df\Core\O::cachedGlobal()
	 * @return string[]
	 */
	protected function cachedGlobal() {return self::_m(__CLASS__, 'responseA');}

	/**
	 * 2015-11-27
	 * @return array(string => Font)
	 */
	private function items() {return dfc($this, function() {
		/** @var mixed $result */
		/** @var Font[] $fonts */
		$fonts = array_map(function(array $itemA) {return new Font($itemA);}, $this->responseA());
		/** @var string[] $families */
		$families = array_map(function(Font $font) {return $font->family();}, $fonts);
		return array_combine($families, $fonts);
	});}

	/**
	 * 2015-11-27
	 * @return array(string => mixed)
	 * @throws \Exception
	 */
	private function responseA() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $debug */
			$debug = true;
			/** @var array(string => mixed) $result */
			$result = df_json_decode(
				$debug || !S::s()->serverApiKey()
				? df_http_get('https://mage2.pro/google-fonts.json')
				: df_http_get('https://www.googleapis.com/webfonts/v1/webfonts', [
					'key' => S::s()->serverApiKey(), 'sort' => 'alpha'
				])
			);
			/**
			 * 2015-11-17
			 * В документации об этом ни слова не сказано,
			 * однако в случае сбоя Google API возвращает JSON следующией структуры:
				{
					error: {
						errors: [
							{
								domain: "usageLimits",
								reason: "accessNotConfigured",
								message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration.",
								extendedHelp: "https://console.developers.google.com"
							}
						],
						code: 403,
						message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration."
					}
				}
			 * https://developers.google.com/fonts/docs/developer_api
			 */
			/** @var array(string => mixed)|null $result */
			$error = dfa($result, 'error');
			if ($error) {
				throw (new Exception($error))->standard();
			}
			/**
			 * 2015-11-27
			 * https://developers.google.com/fonts/docs/developer_api#Example
			 */
			$result = dfa($result, 'items');
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}