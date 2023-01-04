<?php
namespace Df\GoogleFont\Fonts;
use Df\GoogleFont\Font;
use Df\GoogleFont\Font\Variant;
use Df\GoogleFont\Font\Variant\Preview;
use Df\GoogleFont\Font\Variant\Preview\Params;
use Df\GoogleFont\Fonts;
final class Sprite extends Png {
	/**
	 * 2015-12-08
	 * Возвращает координаты левого верхнего угла изображения шрифта в общей картинке-спрайте.
	 * Клиентская часть затем использует эти координаты в правиле CSS background-position:
	 * https://developer.mozilla.org/en-US/docs/Web/CSS/background-position
	 * https://developer.mozilla.org/en-US/docs/Web/CSS/position_value
	 * Обратите внимание, что размеры изображения шрифта мы клиентской части не передаём,
	 * потому что клиентская часть сама передала их нам и знает их.
	 * @return int[]
	 */
	function datumPoint(Preview $preview):array {return dfa($this->datumPoints(), $preview->getId());}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\GoogleFont\Fonts\Png::draw()
	 * @used-by \Df\GoogleFont\Fonts\Png::image()
	 * @param resource $image
	 */
	protected function draw($image):void {
		$x = 0; /** @var int $x */
		$y = 0; /** @var int $y */
		$this->_datumPoints = [];
		df_assert_nef(imagefill($image, 0, 0, $this->colorAllocateAlpha($image, $this->bgColor())));
		# http://stackoverflow.com/a/1397584/254475
		imagealphablending($image, true);
		foreach ($this->previews() as $preview) { /** @var Preview $preview */
			try {
				$previewImage = df_assert_nef(imagecreatefromstring($preview->contents())); /** @var resource $previewImage */
				try {
					df_assert_nef(imagecopy($image, $previewImage, $x, $y, 0, 0, $preview->width(), $preview->height()));
					$this->_datumPoints[$preview->getId()] = [$x, $y];
				}
				finally {
					imagedestroy($previewImage);
				}
			}
			catch (\Exception $e) {
				df_log($e->getMessage());
			}
			$x += $this->previewWidth();
			if ($x >= $this->width()) {
				$x = 0;
				$y += $this->previewHeight() + $this->marginY();
			}
		}
		df_file_write($this->pathToDatumPoints(), df_json_encode($this->_datumPoints));
	}

	/**
	 * 2015-12-08 Высота спрайта
	 * 2015-12-10
	 * 1) Результат $this->square() / $this->width() может быть дробным (например: 3796.1538461538),
	 * т.к. мы уже провели много замысловатых операций.
	 * Поэтому для приведения результату к целому типу надло использоват именно @uses ceil(),
	 * иначе последнему ряду спрайта не хватит места.
	 * 2) Также надо учитывать, что последний ряд почти наверняка будет заполнен не полностью,
	 * а наш алгоритм вычисления площади исходит из суммы площадей миниатюр,
	 * т.е. считает, что последний ряд заполнен полностью.
	 * Поэтому добавляем место для ещё одого ряда снизу, чтобы последнему ряду уж наверняка хватило места.
	 * @override
	 * @see \Df\GoogleFont\Fonts\Png::height()
	 * @used-by \Df\GoogleFont\Fonts\Png::image()
	 */
	protected function height():int {return ceil($this->square() / $this->width()) + $this->previewHeight() + $this->marginY();}

	/**
	 * 2015-12-08 Кэшировать результат нельзя!
	 * @override
	 * @see \Df\GoogleFont\Fonts\Png::needToCreate()
	 * @used-by \Df\GoogleFont\Fonts\Png::createIfNeeded()
	 */
	protected function needToCreate():bool {return !file_exists($this->pathToDatumPoints()) || parent::needToCreate();}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\GoogleFont\Fonts\Png::pathRelativeA()
	 * @used-by \Df\GoogleFont\Fonts\Png::path()
	 * @return string[]
	 */
	protected function pathRelativeA():array {return [$this->pathRelativeBase(), $this->fs()->namePng(['i'])];}

	/**
	 * 2015-12-08
	 *
	 * Ширина спрайта.
	 * Изначально я делал ширину равной максимальной из ширин картинок спрайта (а картинки по сути были одинаковой ширины).
	 * В таком случае спрайт получается узкий по ширине и длинный по высоте.
	 * Намного удобнее его смотреть (ну, для тестирования), когда его ширина и высота примерно равны друг другу,
	 * поэтому чуть переделал алгоритм.
	 * @override
	 * @see \Df\GoogleFont\Fonts\Png::width()
	 * @used-by \Df\GoogleFont\Fonts\Png::image()
	 */
	protected function width():int {return $this->numPreviewsInARow() * $this->previewWidth();}

	/**
	 * 2015-12-08
	 * @used-by self::datumPoint()
	 * @return array(string => int[])
	 */
	private function datumPoints():array {
		if (!$this->_datumPoints) {
			if (file_exists($this->pathToDatumPoints())) {
				try {
					$this->_datumPoints = df_json_decode(df_file_read($this->pathToDatumPoints()));
				}
				catch (\Exception $e) {
					df_log($e->getMessage());
				}
			}
			if (!$this->_datumPoints) {
				$this->create();
				df_assert_array($this->_datumPoints);
			}
		}
		return $this->_datumPoints;
	}

	/**
	 * 2015-12-10
	 * Межстрочное расстояние.
	 * Текст на миниатюре прижат к нижнему краю холста.
	 * Бывает, что в браузере нам требуется сдвинуть текст чуть вверх.
	 * Конечно, это в простом случае можно сделать средствами CSS, однако хорошо иметь и возможность двигать холст.
	 * @used-by self::draw()
	 * @used-by self::height()
	 * @used-by self::square()
	 */
	private function marginY():int {return 4;}

	/**
	 * 2015-12-08 Количество картинок в одном горизонтальном ряду спрайта.
	 * @used-by self::width()
	 */
	private function numPreviewsInARow():int {return ceil(sqrt($this->square()) / $this->previewWidth());}

	/**
	 * 2015-12-08
	 * @used-by self::pathRelativeA()
	 * @used-by self::pathToDatumPoints()
	 */
	private function pathRelativeBase():string {return dfc($this, function():string {return
		df_cc_path('sprite', df_fs_name(implode('_', [$this->fs()->nameResolution(), $this->fs()->nameColorsSizeMargin()])))
	;});}

	/**
	 * 2015-12-08
	 * @used-by self::datumPoints()
	 * @used-by self::draw()
	 * @used-by self::needToCreate()
	 */
	private function pathToDatumPoints():string {return dfc($this, function():string {return $this->fs()->absolute([
		$this->pathRelativeBase(), 'datum-points.json'
	]);});}

	/**
	 * @used-by self::draw()
	 * @used-by self::height()
	 * @used-by self::square()
	 */
	private function previewHeight():int {return $this->params()->height();}

	/**
	 * 2015-12-08
	 * 2017-01-12: https://3v4l.org/9YXir
	 * @return Preview[]
	 */
	private function previews():array {return dfc($this, function():array {return
		array_merge(...df_map(function(Font $font):array {return
			array_map(function(Variant $variant):Preview {return
				$variant->preview()
			;}, array_values($font->variantsAvailable()))
		;}, $this[self::$P__FONTS]))
	;});}

	/**
	 * @used-by self::draw()
	 * @used-by self::numPreviewsInARow()
	 * @used-by self::square()
	 * @used-by self::width()
	 */
	private function previewWidth():int {return $this->params()->width();}

	/**
	 * 2015-12-08 Площадь спрайта: сумм площадей всех картинок спрайта.
	 */
	private function square():int {return dfc($this, function():int {return
		($this->previewHeight() + $this->marginY()) * $this->previewWidth() * count($this->previews())
	;});}

	/** @var string */
	private static $P__FONTS = 'fonts';

	/**
	 * 2015-12-08
	 * @used-by self::datumPoint()
	 * @var array(string => int[])
	 */
	private $_datumPoints = [];

	/**
	 * 2015-12-08
	 * @param Fonts $fonts
	 * @param Params $params
	 * @return \Df\GoogleFont\Fonts\Sprite
	 */
	static function i(Fonts $fonts, Params $params) {return new self([self::$P__FONTS => $fonts, self::$P__PARAMS => $params]);}
}