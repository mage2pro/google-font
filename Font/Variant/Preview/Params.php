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
	 * @return int
	 */
	function fontSize():int {return $this[self::$P__FONT_SIZE];}

	/**
	 * 2015-12-01
	 * @used-by \Df\GoogleFont\Font\Variant::preview()
	 * @return string
	 */
	function getId() {return dfc($this, function() {return implode('-', [
		$this->width()
		,$this->height()
		,$this->fontSize()
		,implode('-', $this->fontColor())
		,implode('-', $this->bgColor())
	]);});}

	/** @return int */
	function height() {return $this[self::$P__HEIGHT];}

	/**
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::draw()
	 * @used-by \Df\GoogleFont\Fonts\Fs::nameColorsSizeMargin()
	 */
	function marginLeft():int {return $this[self::$P__MARGIN_LEFT];}

	/** @return int */
	function width() {return $this[self::$P__WIDTH];}

	/**
	 * @param $colorS
	 * @return int[]
	 */
	private function rgb($colorS) {return df_int(explode('|', $colorS));}

	/**
	 * Этот метод возвращает объект-одиночку,
	 * потому что параметры запроса у нас неизменны в течение всей жизни запроса.
	 * @used-by \Df\GoogleFont\Font\Variant::preview()
	 * @return Params
	 */
	static function fromRequest() {
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

	/** @var string */
	private static $P__BG_COLOR = 'bgColor';
	/** @var string */
	private static $P__FONT_COLOR = 'fontColor';
	/** @var string */
	private static $P__FONT_SIZE = 'fontSize';
	/** @var string */
	private static $P__HEIGHT = 'height';
	/** @var string */
	private static $P__MARGIN_LEFT = 'marginLeft';
	/** @var string */
	private static $P__WIDTH = 'width';
}