<?php
namespace Df\GoogleFont\Font;
use Df\GoogleFont\Font;
use Df\GoogleFont\Font\Variant\Preview;
use Df\GoogleFont\Font\Variant\Preview\Params;
use Df\GoogleFont\Fonts\Fs;
final class Variant extends \Df\Core\O {
	/**
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::folderFamily()
	 * @return Font
	 */
	function font() {return $this[self::$P__FONT];}

	/**
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::baseName()
	 * @return string
	 */
	function name() {return $this[self::$P__NAME];}

	/**
	 * 2015-11-29
	 * @param Params|null $params [optional]
	 * @return Preview
	 */
	function preview(Params $params = null) {
		if (!$params) {
			$params = Params::fromRequest();
		}
		if (!isset($this->{__METHOD__}[$params->getId()])) {
			$this->{__METHOD__}[$params->getId()] = Preview::i($this, $params);
		}
		return $this->{__METHOD__}[$params->getId()];
	}

	/**
	 * 2015-11-30
	 * @used-by \Df\GoogleFont\Font\Variant\Preview
	 * @return string
	 */
	function ttfPath() {return dfc($this, function() {
		/** @var string $result */
		if (!file_exists($result = Fs::s()->absolute(['ttf', basename($this->url())]))) {
			df_file_write($result, file_get_contents($this->url()));
		}
		return $result;
	});}

	/** @return string */
	private function url() {return $this[self::$P__URL];}

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