<?php
namespace Df\GoogleFont\Font;
use Df\GoogleFont\Font;
use Df\GoogleFont\Font\Variant\Preview;
use Df\GoogleFont\Font\Variant\Preview\Params;
use Df\GoogleFont\Fonts\Fs;
final class Variant {
    /**
     * 2022-12-31
	 * @used-by \Df\GoogleFont\Font::variants()
     */
    function __construct(Font $font, string $name, string $url) {
        $this->_font = $font; $this->_name = $name; $this->_url = $url;
    }

	/** @used-by \Df\GoogleFont\Font\Variant\Preview::folderFamily() */
	function font():Font {return $this->_font;}

	/** @used-by \Df\GoogleFont\Font\Variant\Preview::baseName() */
	function name():string {return $this->_name;}

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
		if (!file_exists($r = Fs::s()->absolute(['ttf', basename($this->_url)]))) {
			df_file_write($r, df_contents($this->_url));
		}
		return $r;
	});}

    /**
	 * 2022-12-31
	 * @used-by self::__construct()
	 * @used-by self::font()
     * @var Font
     */
	private $_font;

    /**
	 * 2022-12-31
	 * @used-by self::__construct()
	 * @used-by self::name()
     * @var string
     */
	private $_name;

    /**
	 * 2022-12-31
	 * @used-by self::__construct()
	 * @used-by self::ttfPath()
     * @var string
     */
	private $_url;
}