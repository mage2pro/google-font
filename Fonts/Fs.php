<?php
namespace Df\GoogleFont\Fonts;
use Df\GoogleFont\Font\Variant\Preview\Params;
# 2015-12-08
class Fs {
	/**
	 * 2015-12-08
	 * @used-by \Df\GoogleFont\Font\Variant::ttfPath()
	 * @used-by \Df\GoogleFont\Fonts\Png::path()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::pathToDatumPoints()
	 * @param string[] $rel
	 */
	function absolute(array $rel):string {return df_media_path_absolute('df/api/google/fonts/'). df_cc_path($rel);}

	/**
	 * 2015-12-08
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::pathRelativeA()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::pathRelativeBase()
	 */
	function nameColorsSizeMargin():string {return dfc($this, function():string {$p = $this->p(); return
		implode('_', [
			's' . df_pad0(2, $p->fontSize())
			,'f' . implode('-', $p->fontColor())
			,'b' . implode('-', $p->bgColor())
			,'m' . $p->marginLeft()
		])
	;});}

	/**
	 * 2015-12-08
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::pathRelativeA()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::pathRelativeA()
	 * @param string[] $p
	 */
	function namePng(array $p):string {return df_fs_name(implode('_', $p)) . '.png';}

	/**
	 * 2015-12-08
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::pathRelativeA()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::pathRelativeBase()
	 */
	function nameResolution():string {$p = $this->p(); return implode('x', [$p->width(), $p->height()]);}

	/** @return Params */
	private function p() {return Params::fromRequest();}

	/**
	 * 2017-03-15
	 * @used-by \Df\GoogleFont\Font\Variant::ttfPath()
	 */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}