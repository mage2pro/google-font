<?php
namespace Dfe\GoogleFont\Fonts;
use Dfe\GoogleFont\Font\Variant\Preview\Params;
/**
 * @see \Dfe\GoogleFont\Font\Variant\Preview
 * @see \Dfe\GoogleFont\Fonts\Sprite
 */
abstract class Png extends \Df\Core\O {
	/**
	 * 2015-12-08
	 * @used-by self::image()
	 * @see \Dfe\GoogleFont\Font\Variant\Preview::draw()
	 * @see \Dfe\GoogleFont\Fonts\Sprite::draw()
	 * @param resource $image
	 */
	abstract protected function draw($image):void;

	/**
	 * 2015-12-08
	 * @used-by self::image()
	 * @see \Dfe\GoogleFont\Font\Variant\Preview::height()
	 * @see \Dfe\GoogleFont\Fonts\Sprite::height()
	 */
	abstract protected function height():int;

	/**
	 * 2015-12-08
	 * @used-by self::image()
	 * @see \Dfe\GoogleFont\Font\Variant\Preview::width()
	 * @see \Dfe\GoogleFont\Fonts\Sprite::width()
	 */
	abstract protected function width():int;

	/**
	 * 2015-12-08
	 * @used-by self::path()
	 * @see \Dfe\GoogleFont\Font\Variant\Preview::pathRelativeA()
	 * @see \Dfe\GoogleFont\Fonts\Sprite::pathRelativeA()
	 * @return string[]
	 */
	abstract protected function pathRelativeA():array;

	/**
	 * @used-by \Dfe\GoogleFont\Controller\Index\Preview::contents()
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::draw()
	 */
	final function contents():string {return dfc($this, function():string {
		$this->createIfNeeded();
		return df_contents($this->path());
	});}

	/**
	 * 2015-12-01
	 * 1) Изначально реализация была «ленивой»:
	 *		$this->exists()
	 *		? df_media_path2url($this->path())
	 *		: df_url_frontend('df-api/google/fontPreview', ['_query' => [
	 *			'family' => implode(':', [$this->family(), $this->variant()->name()])
	 *		] + $this->params()->getData()])
	 * Однако оказалось, что она крайне неэффективна: в клиентской части мы создаём много тегов IMG,
	 * и при добавлении в DOM браузер сразу делает кучу запросов к серверу по адресу src.
	 * Получается, что намного эффективнее сразу построить все картинки в едином запросе.
	 * 2) Но df-api/google/fontPreview нам всё равно пригодится для динамических запросов!
	 * @used-by \Dfe\GoogleFont\Controller\Index\Index::execute()
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::isAvailable()
	 */
	final function url():string {return dfc($this, function():string {return df_try(
		function() {$this->createIfNeeded(); return df_media_path2url($this->path());}
		,function(\Throwable $t) {df_log($t); return '';}
	);});}

	/**
	 * 2015-11-30
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::draw()
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::draw()
	 * @return int[]
	 */
	final protected function bgColor():array {return $this->params()->bgColor();}

	/**
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::draw()
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::draw()
	 * @param resource $image
	 * @param int[] $rgba
	 */
	final protected function colorAllocateAlpha($image, array $rgba):int {return df_assert_nef(
		imagecolorallocatealpha($image, $rgba[0], $rgba[1], $rgba[2], dfa($rgba, 3, 0))
	);}

	/**
	 * 2015-12-08
	 * @used-by self::createIfNeeded()
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::datumPoints()
	 */
	final protected function create():void {
		ob_start();
		try {
			$i = $this->image(); /** @var resource $i */
			try {imagepng($this->image());}
			finally {imagedestroy($i);}
			df_file_write($this->path(), ob_get_contents());
		}
		finally {ob_end_clean();}
	}

	/**
	 * 2015-12-08
	 * @used-by self::contents()
	 * @used-by self::url()
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::datumPoint()
	 */
	final protected function createIfNeeded():void {
		if ($this->needToCreate()) {
			$this->create();
		}
	}

	/**
	 * @used-by self::path()
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::pathRelativeA()
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::pathRelativeA()
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::pathToDatumPoints()
	 */
	final protected function fs():Fs {return Fs::s();}

	/**
	 * 2015-12-08 Кэшировать результат нельзя!
	 * @used-by self::createIfNeeded()
	 * @see \Dfe\GoogleFont\Fonts\Sprite::needToCreate()
	 */
	protected function needToCreate():bool {return !file_exists($this->path());}

	/**
	 * @used-by self::bgColor()
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::draw()
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::fontSize()
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::height()
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::width()
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::previewHeight()
	 */
	final protected function params():Params {return $this[self::$P__PARAMS];}

	/**
	 * 2015-12-08
	 * @used-by self::create()
	 * @return resource
	 */
	private function image() {
		$r = df_assert_nef(imagecreatetruecolor($this->width(), $this->height())); /** @var resource|bool $r */
		df_assert(imagesavealpha($r, true));
		$this->draw($r);
		return $r;
	}

	/**
	 * @used-by self::contents()
	 * @used-by self::create()
	 * @used-by self::needToCreate()
	 * @used-by self::url()
	 */
	private function path():string {return dfc($this, function() {return $this->fs()->absolute($this->pathRelativeA());});}

	/**
	 * @used-by self::params()
	 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::i()
	 * @used-by \Dfe\GoogleFont\Fonts\Sprite::i()
	 * @var string
	 */
	protected static $P__PARAMS = 'params';
}