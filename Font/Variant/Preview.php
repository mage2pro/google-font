<?php
namespace Df\GoogleFont\Font\Variant;
use Df\GoogleFont\Font\Variant;
use Df\GoogleFont\Font\Variant\Preview\Params;
final class Preview extends \Df\GoogleFont\Fonts\Png {
	/**
	 * 2015-12-08
	 * Стандартный способ генерации идентификатора нас не устраивает,
	 * потому что он создаёт идентификатор случайным образом,
	 * а нам нужно, чтобы идентификатор был одним и тем же
	 * для двух любых запросов к серверу (чтобы сопоставлять preview и datumPoints).
	 * @used-by \Df\GoogleFont\Fonts\Sprite::datumPoint()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
	 * @return string|int
	 */
	function getId() {return dfc($this, function() {return implode(':', [$this->family(), $this->variant()->name()]);});}

	/**
	 * 2015-12-08
	 * @used-by \Df\GoogleFont\Font::variantsAvailable()
	 */
	function isAvailable():bool {return !!$this->url();}

	/**
	 * 2015-12-08
	 * Точка отсчёта системы координат [0, 0] — это самая левая верхняя точка холста.
	 * Далее кординаты увеличиваются вниз и вправо.
	 * @override
	 * @see \Df\GoogleFont\Fonts\Png::draw()
	 * @used-by \Df\GoogleFont\Fonts\Png::image()
	 * @param resource $image
	 */
	protected function draw($image) {
		df_assert(imagefill($image, 0, 0, $this->colorAllocateAlpha($image, $this->bgColor())));
		df_assert(imagettftext(
			$image
			,$this->fontSize()
			,0
			,$this->marginLeft()
			/**
			 * 2015-12-10
			 * The y-ordinate.
			 * This sets the position of the fonts baseline, not the very bottom of the character.
			 * https://php.net/manual/function.imagettftext.php
			 * Если мы хотим, чтобы нижняя часть текста была прилеплена к нижней части холста,
			 * надо указать здесь высоту холста
			 * минус то расстояние, на которое текст опускается ниже своей baseline.
			 */
			,$this->height() - abs(
				$this->box(1) # 2015-12-10 Нижняя координата Y отображаемого текста.
			)
			,$this->colorAllocateAlpha($image, $this->params()->fontColor())
			,$this->ttfPath()
			,$this->text()
		));
	}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\GoogleFont\Fonts\Png::height()
	 * @used-by \Df\GoogleFont\Fonts\Png::image()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::height()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
	 * @return int
	 */
	protected function height() {return $this->params()->height();}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\GoogleFont\Fonts\Png::pathRelativeA()
	 * @used-by \Df\GoogleFont\Fonts\Png::path()
	 * @return string[]
	 */
	protected function pathRelativeA() {return [
		'preview'
		, df_fs_name($this->family())
		, $this->fs()->nameResolution()
		, $this->fs()->namePng([$this->variant()->name(), $this->fs()->nameColorsSizeMargin()])
	];}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\GoogleFont\Fonts\Png::width()
	 * @used-by \Df\GoogleFont\Fonts\Png::image()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::width()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
	 * @return int
	 */
	protected function width() {return $this->params()->width();}

	/**
	 * 2015-12-10
	 * https://php.net/manual/function.imagettfbbox.php
	 *
	 * Пример $box для шрифта «ABeeZee (regular)»:
	 * [-1, 4, 159, 4, 159 -16, -1, -16]
	 * Bottom Left: [-1, 4]
	 * Bottom Right: [159, 4]
	 * Top Right: [159, -16]
	 * Top Left: [-1, -16]
	 *
	 * Прочитайте в Википедии статью про baseline (и смотрите там картинку):
	 * https://en.wikipedia.org/wiki/Baseline_(typography)
	 *
	 * Точка отсчёта системы координат [0, 0] — это самая левая точка baseline.
	 * Далее кординаты увеличиваются вниз и вправо.
	 * В описанном выше примере нижняя координата Y имеет значение 4:
	 * это значит, что самая нижняя точка текста расположена на 4 пикселя ниже baseline.
	 * Верхяя координата Y имеет значение -16:
	 * это значит, что самая верхняя точка текста расположена на 16 пикселей выше baseline.
	 *
	 * Почему левая координата X получилась отрицательной?
	 * Видимо, из-за левой засечки первой буквы текста «A».
	 *
	 * @used-by self::draw()
	 * @param int $index
	 * @throws \Exception
	 */
	private function box($index):int {return dfc($this, function() {return df_try(
		function() {return df_assert_nef(imagettfbbox($this->fontSize(), 0, $this->ttfPath(), $this->text()));}
		,function(\Exception $e) {
			throw new \Exception(
				'Unable to load the TTF file for the font'
				." «{$this->family()} ({$this->variant()->name()})»: «{$this->ttfPath()}»."
				."\n" . $e->getMessage()
				, 0, $e
			);
		}
	);})[$index];}

	/**
	 * @used-by self::box()
	 * @used-by self::getId()
	 * @used-by self::text()
	 * @return string
	 */
	private function family() {return $this->variant()->font()->family();}

	/** @return int */
	private function fontSize() {return $this->params()->fontSize();}

	/** @return int */
	private function marginLeft() {return $this->params()->marginLeft();}

	/**
	 * 2015-12-10 Текст для отображения.
	 * @used-by \Df\GoogleFont\Font\Variant\Preview::draw()
	 * @return string
	 */
	private function text() {return dfc($this, function() {return df_desc($this->family(), $this->variant()->name());});}

	/** @return string */
	private function ttfPath() {return $this->variant()->ttfPath();}

	/** @return Variant */
	private function variant() {return $this[self::$P__VARIANT];}
	/**
	 * 2015-11-29
	 * @used-by \Df\GoogleFont\Font\Variant::preview()
	 * @param Variant $variant
	 * @param Params $params
	 * @return Preview
	 */
	static function i(Variant $variant, Params $params) {return new self([
		self::$P__VARIANT => $variant, self::$P__PARAMS => $params
	]);}

	/** @var string */
	private static $P__VARIANT = 'variant';
}
