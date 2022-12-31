<?php
namespace Df\GoogleFont;
use Df\GoogleFont\Font\Variant;
/**
 * 2015-11-27
 * https://developers.google.com/fonts/docs/developer_api#Example
 *		{
 *			"kind": "webfonts#webfont",
 *			"family": "ABeeZee",
 *			"category": "sans-serif",
 *			"variants": ["regular", "italic"],
 *			"subsets": ["latin"],
 *			"version": "v4",
 *			"lastModified": "2015-04-06",
 *			"files": {
 *				"regular": "http://fonts.gstatic.com/s/abeezee/v4/mE5BOuZKGln_Ex0uYKpIaw.ttf",
 *				"italic": "http://fonts.gstatic.com/s/abeezee/v4/kpplLynmYgP0YtlJA3atRw.ttf"
 *			}
 *		},
 *		{
 *			"kind": "webfonts#webfont",
 *			"family": "Abel",
 *			"category": "sans-serif",
 *			"variants": ["regular"],
 *			"subsets": ["latin"],
 *			"version": "v6",
 *			"lastModified": "2015-04-06",
 *			"files": {"regular": "http://fonts.gstatic.com/s/abel/v6/RpUKfqNxoyNe_ka23bzQ2A.ttf"}
 *		}
 */
final class Font extends \Df\Core\O {
	/**
	 * 2015-11-28 "family": "ABeeZee"
	 */
	function family():string {return $this['family'];}

	/**
	 * 2015-11-29
	 * @param string $name
	 * @return Variant
	 * @throws \Exception
	 */
	function variant($name) {
		$r = dfa($this->variants(), $name); /** @var Variant|null $r */
		if (!$r) {
			throw new \Exception("Variant «{$name}» of font «{$this->family()}» is not found.");
		}
		return $r;
	}

	/**
	 * 2015-11-28 "variants": ["regular", "italic"]
	 * @return string[]
	 */
	function variantNames() {return $this['variants'];}

	/**
	 * 2015-11-27
	 * @return array(string => Variant)
	 */
	function variants() {return dfc($this, function() {return array_combine(
		$this->variantNames()
		,array_map(function($name) {return Variant::i($this, $name, $this['files'][$name]);}, $this->variantNames())
	);});}

	/**
	 * 2015-12-08
	 * @return array(string => Variant)
	 */
	function variantsAvailable() {return dfc($this, function() {return array_filter(
		$this->variants(), function(Variant $variant) {return
			$variant->preview()->isAvailable()
		;}
	);});}
}