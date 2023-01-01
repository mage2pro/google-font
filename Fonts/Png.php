<?php
namespace Df\GoogleFont\Fonts;
use Df\GoogleFont\Font\Variant\Preview\Params;
/**
 * @see \Df\GoogleFont\Font\Variant\Preview
 * @see \Df\GoogleFont\Fonts\Sprite
 */
abstract class Png extends \Df\Core\O {
	/**
	 * 2015-12-08
	 * @used-by self::image()
	 * @see \Df\GoogleFont\Font\Variant\Preview::draw()
	 * @see \Df\GoogleFont\Fonts\Sprite::draw()
	 * @param resource $image
	 */
	abstract protected function draw($image):void;

	/**
	 * 2015-12-08
	 * @used-by self::image()
	 * @see \Df\GoogleFont\Font\Variant\Preview::height()
	 * @see \Df\GoogleFont\Fonts\Sprite::height()
	 */
	abstract protected function height():int;

	/**
	 * 2015-12-08
	 * @used-by self::image()
	 * @see \Df\GoogleFont\Font\Variant\Preview::width()
	 * @see \Df\GoogleFont\Fonts\Sprite::width()
	 */
	abstract protected function width():int;

	/**
	 * 2015-12-08
	 * @used-by self::path()
	 * @see \Df\GoogleFont\Font\Variant\Preview::pathRelativeA()
	 * @see \Df\GoogleFont\Fonts\Sprite::pathRelativeA()
	 * @return string[]
	 */
	abstract protected function pathRelativeA();

	/**
	 * @used-by \Df\GoogleFont\Controller\Index\Preview::contents()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
	 * @return string
	 */
	function contents():string {return dfc($this, function() {
		$this->createIfNeeded();
		return df_contents($this->path());
	});}

	/**
	 * 2015-12-01
	 * Изначально реализация была «ленивой»:
	 *		$this->exists()
	 *		? df_media_path2url($this->path())
	 *		: df_url_frontend('df-api/google/fontPreview', ['_query' => [
	 *			'family' => implode(':', [$this->family(), $this->variant()->name()])
	 *		] + $this->params()->getData()])
	 * Однако оказалось, что она крайне неэффективна:
	 * в клиентской части мы создаём много тегов IMG, и при добавлении в DOM
	 * браузер сразу делает кучу запросов к серверу по адресу src.
	 * Получается, что намного эффективнее сразу построить все картинки в едином запросе.
	 *
	 * Но df-api/google/fontPreview нам всё равно пригодится для динамических запросов!
	 *
	 * @return string
	 */
	function url() {return dfc($this, function() {return df_try(
		function() {$this->createIfNeeded(); return df_media_path2url($this->path());}
		,function(\Exception $e) {df_log($e->getMessage()); return '';}
	);});}

	/**
	 * 2015-11-30
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::draw()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
	 * @return int|int[]
	 */
	final protected function bgColor() {return $this->params()->bgColor();}

	/**
	 * @param resource $image
	 * @param int[] $rgba
	 * @return int
	 */
	protected function colorAllocateAlpha($image, array $rgba) {return df_assert_nef(
		imagecolorallocatealpha($image, $rgba[0], $rgba[1], $rgba[2], dfa($rgba, 3, 0))
	);}

	/**
	 * 2015-12-08
	 * @used-by self::createIfNeeded()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::datumPoints()
	 */
	protected function create() {
		ob_start();
		try {
			$image = $this->image();
			try {imagepng($this->image());}
			finally {imagedestroy($image);}
			df_file_write($this->path(), ob_get_contents());
		}
		finally {ob_end_clean();}
	}

	/**
	 * 2015-12-08
	 * @used-by self::contents()
	 * @used-by self::url()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::datumPoint()
	 */
	protected function createIfNeeded() {
		if ($this->needToCreate()) {
			$this->create();
		}
	}

	/** @return Fs */
	protected function fs() {return Fs::s();}

	/**
	 * 2015-12-08 Кэшировать результат нельзя!
	 * @used-by self::createIfNeeded()
	 * @see \Df\GoogleFont\Fonts\Sprite::needToCreate()
	 * @return bool
	 */
	protected function needToCreate() {return !file_exists($this->path());}

	/**
	 * @used-by self::draw()
	 * @return Params
	 */
	protected function params() {return $this[self::$P__PARAMS];}

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

	/** @return string */
	private function path() {return dfc($this, function() {return $this->fs()->absolute($this->pathRelativeA());});}

	/** @var string */
	protected static $P__PARAMS = 'params';
}