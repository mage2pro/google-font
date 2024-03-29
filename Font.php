<?php
namespace Dfe\GoogleFont;
use Df\Core\Exception as DFE;
use Dfe\GoogleFont\Font\Variant as V;
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
	 * @used-by self::variant()
	 * @used-by \Dfe\GoogleFont\Fonts::items()
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::family()
	 */
	function family():string {return $this['family'];}

	/**
	 * 2015-11-29
	 * @used-by \Dfe\GoogleFont\Controller\Index\Preview::contents()
	 * @throws DFE
	 */
	function variant(string $n):V {return df_assert(dfa($this->variants(), $n),
		"The variant «{$n}» of the font «{$this->family()}» has not been found."
	);}

	/**
	 * 2015-11-27
	 * @used-by self::variant()
	 * @used-by self::variantsAvailable()
	 * @used-by \Dfe\GoogleFont\Controller\Index\Index::execute()
	 * @return array(string => V)
	 */
	function variants():array {return dfc($this, function():array {
		# 2015-11-28 "variants": ["regular", "italic"]
		$nn = $this['variants']; /** @var string[] $nn */
		return array_combine($nn, array_map(function(string $n):V {return new V($this, $n, $this['files'][$n]);}, $nn));
	});}

	/**
	 * 2015-12-08
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::previews()
	 * @return array(string => V)
	 */
	function variantsAvailable():array {return dfc($this, function():array {return array_filter(
		$this->variants(), function(V $v):bool {return $v->preview()->isAvailable();}
	);});}
}