<?php
namespace Df\GoogleFont\Fonts;
use Df\GoogleFont\Font\Variant\Preview\Params;
# 2015-12-08
class Fs {
	/**
	 * 2015-12-08
	 * @param string[] $relativeParts
	 * @return string
	 */
	function absolute(array $relativeParts) {return $this->baseAbsolute() . df_cc_path($relativeParts);}

	/**
	 * 2015-12-08
	 * @return string
	 */
	function nameColorsSizeMargin() {return dfc($this, function() {$p = $this->params(); return
		implode('_', [
			's' . df_pad0(2, $p->fontSize())
			,'f' . implode('-', $p->fontColor())
			,'b' . implode('-', $p->bgColor())
			,'m' . $p->marginLeft()
		])
	;});}

	/**
	 * 2015-12-08
	 * @param string[] $params
	 * @return string
	 */
	function namePng(array $params) {return df_fs_name(implode('_', $params)) . '.png';}

	/**
	 * 2015-12-08
	 * @return string
	 */
	function nameResolution() {return dfc($this, function() {$p = $this->params(); return
		implode('x', [$p->width(), $p->height()])
	;});}

	/** @return string */
	private function baseAbsolute() {return df_media_path_absolute(self::baseRelative());}

	/** @return string */
	private function baseRelative() {return df_cc_path_t('df', 'api', 'google', 'fonts');}

	/** @return Params */
	private function params() {return Params::fromRequest();}

	/**
	 * 2017-03-15
	 * @return self
	 */
	static function s() {static $r; return $r ? $r : $r = new self;}
}