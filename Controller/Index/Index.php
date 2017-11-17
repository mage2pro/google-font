<?php
namespace Df\GoogleFont\Controller\Index;
use Df\Framework\W\Result\Json;
use Df\GoogleFont\Font;
use Df\GoogleFont\Font\Variant;
use Df\GoogleFont\Font\Variant\Preview\Params;
use Df\GoogleFont\Fonts as _Fonts;
use Df\GoogleFont\Fonts\Sprite;
// 2015-12-08
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * 2015-12-09
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * На странице может быть сразу несколько данных элементов управления.
	 * Возникает проблема: как синхронизировать их одновременные обращения к серверу за данными?
	 * Проблемой это является, потому что генерация образцов шрифтов —
	 * длительная (порой минуты) задача со множеством файловых операций.
	 * Параллельный запуск сразу двух таких генераций
	 * (а они будут выполняться разными процессами PHP)
	 * почти наверняка приведёт к файловым конфликтам и ошибкам,
	 * да и вообще смысла в этом никакого нет:
	 * зачем параллельно делать одно и то же с одними и теми же объектами?
	 * Эта проблема была решена в серверной части применением функции @uses df_sync
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Json
	 */
	function execute() {
		df_response_cache_max();
		$this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
		return df_sync($this, function() {return Json::i(df_cache_get_simple(df_request(), function() {return
			df_json_encode([
				'sprite' => $this->sprite()->url()
				,'fonts' => array_filter(df_map(function(Font $font) {return array_filter(array_map(
					function(Variant $variant) {return $this->sprite()->datumPoint($variant->preview());}
					,$font->variants()
				));}, _Fonts::s()))
			])
		;}));});
	}

	/**
	 * 2015-12-08
	 * @used-by execute()
	 * @return Sprite
	 */
	private function sprite() {return dfc($this, function() {return Sprite::i(
		_Fonts::s(), Params::fromRequest()
	);});}
}