<?php
namespace Df\GoogleFont\Font;
use Df\GoogleFont\Font;
use Df\GoogleFont\Font\Variant\Preview;
use Df\GoogleFont\Font\Variant\Preview\Params;
use Df\GoogleFont\Fonts\Fs;
final class Variant extends \Df\Core\O {
	/** @used-by \Df\GoogleFont\Font\Variant\Preview::folderFamily() */
	function font():Font {return $this[self::$P__FONT];}

	/** @used-by \Df\GoogleFont\Font\Variant\Preview::baseName() */
	function name():string {return $this[self::$P__NAME];}

	/**
	 * 2015-11-29
     * @used-by \Df\GoogleFont\Controller\Index\Index::execute()
     * @used-by \Df\GoogleFont\Controller\Index\Preview::contents()
     * @used-by \Df\GoogleFont\Font::variantsAvailable()
     * @used-by \Df\GoogleFont\Fonts\Sprite::previews()
	 */
	function preview(Params $p = null):Preview {
        $p = $p ?: Params::fromRequest();
        return dfc($this, function() use($p):Preview {return Preview::i($this, $p);}, [$p->getId()]);
    }

	/**
	 * 2015-11-30
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::ttfPath()
	 */
	function ttfPath():string {return dfc($this, function():string {/** @var string $r */
		$u = $this[self::$P__URL]; /** @var string $u */
		if (!file_exists($r = Fs::s()->absolute(['ttf', basename($u)]))) {
			df_file_write($r, df_contents($u));
		}
		return $r;
	});}

	/**
	 * 2015-11-29
	 * @param Font $font
	 * @param string $name
	 * @param string $url
	 * @return Variant
	 */
	static function i(Font $font, $name, $url) {return new self([
		self::$P__FONT => $font, self::$P__NAME => $name, self::$P__URL => $url
	]);}

	/** @var string */
	private static $P__FONT = 'font';
	/** @var string */
	private static $P__NAME = 'name';
	/** @var string */
	private static $P__URL = 'url';
}