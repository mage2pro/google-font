<?php
namespace Df\GoogleFont\Font\Variant\Preview;
final class Params  extends \Df\Core\O {
	/**
	 * @used-by self::getId()
	 * @used-by \Df\GoogleFont\Fonts\Fs::nameColorsSizeMargin()
	 * @return int[]
	 */
	function bgColor():array {return $this->rgb($this[self::$P__BG_COLOR]);}

	/**
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::draw()
	 * @used-by \Df\GoogleFont\Fonts\Fs::nameColorsSizeMargin()
	 * @used-by self::getId()
	 * @return int[]
	 */
	function fontColor():array {return $this->rgb($this[self::$P__FONT_COLOR]);}

	/**
	 * @used-by self::getId()
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::fontSize()
	 * @used-by \Df\GoogleFont\Fonts\Fs::nameColorsSizeMargin()
	 */
	function fontSize():int {return $this[self::$P__FONT_SIZE];}

	/**
	 * 2015-12-01
	 * @used-by \Df\GoogleFont\Font\Variant::preview()
	 */
	function getId():string {return dfc($this, function():string {return implode('-', [
		$this->width()
		,$this->height()
		,$this->fontSize()
		,implode('-', $this->fontColor())
		,implode('-', $this->bgColor())
	]);});}

	/**
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::height()
	 * @used-by \Df\GoogleFont\Fonts\Fs::nameResolution()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::previewHeight()
	 */
	function height() {return $this[self::$P__HEIGHT];}

	/**
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::draw()
	 * @used-by \Df\GoogleFont\Fonts\Fs::nameColorsSizeMargin()
	 */
	function marginLeft():int {return $this[self::$P__MARGIN_LEFT];}

	/**
	 * @used-by self::getId()
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::width()
	 */
	function width():int {return $this[self::$P__WIDTH];}

	/**
	 * @used-by self::bgColor()
	 * @used-by self::fontColor()
	 * @return int[]
	 */
	private function rgb(string $colorS):array {return df_int(explode('|', $colorS));}

	/**
	 * Этот метод возвращает объект-одиночку, потому что параметры запроса у нас неизменны в течение всей жизни запроса.
	 * @used-by \Df\GoogleFont\Font\Variant::preview()
	 */
	static function fromRequest():Params {
		$p = [
			self::$P__BG_COLOR => '255|255|255|127'
			,self::$P__FONT_COLOR => '0|0|0|0'
			,self::$P__FONT_SIZE => 14
			,self::$P__HEIGHT => 50
			,self::$P__MARGIN_LEFT => 0
			,self::$P__WIDTH => 400
		]; /** @var array(string => string|int) $p */
		static $r; return $r ? $r : $r = new self(dfa(df_request() + $p, array_keys($p)));
	}

	/**
	 * @used-by self::bgColor()
	 * @used-by self::fromRequest()
	 * @var string
	 */
	private static $P__BG_COLOR = 'bgColor';

	/**
	 * @used-by self::fontColor()
	 * @used-by self::fromRequest()
	 * @var string
	 */
	private static $P__FONT_COLOR = 'fontColor';

	/**
	 * @used-by self::fontSize()
	 * @used-by self::fromRequest()
	 * @var string
	 */
	private static $P__FONT_SIZE = 'fontSize';

	/**
	 * @used-by self::fromRequest()
	 * @used-by self::height()
	 * @var string
	 */
	private static $P__HEIGHT = 'height';

	/**
	 * @used-by self::marginLeft()
	 * @used-by self::fromRequest()
	 * @var string
	 */
	private static $P__MARGIN_LEFT = 'marginLeft';

	/**
	 * @used-by self::STUB()
	 * @used-by self::fromRequest()
	 * @var string
	 */
	private static $P__WIDTH = 'width';
}