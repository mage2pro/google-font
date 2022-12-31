<?php
namespace Df\GoogleFont\Controller\Index;
use Df\GoogleFont\Font\Variant\Preview as VariantPreview;
# 2016-02-17
# Иначе bin/magento setup:di:compile почему-то выдаёт сбой:
# Cannot use Df\GoogleFont\Fonts as Fonts because the name is already in use
# app/code/Df/Api/Controller/Google/FontPreview.php on line 6
use Df\GoogleFont\Fonts as _Fonts;
# 2015-11-29
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Preview extends \Df\Framework\App\Action\Image {
	/**
	 * 2015-11-29
	 * @override
	 * @see \Df\Framework\App\Action\Image::contents()
	 * @used-by \Df\Framework\App\Action\Image::execute()
	 */
	final protected function contents():string {/** @var string[] $familyA */ return
		_Fonts::s()->get(df_first($familyA = explode(':', df_request('family'))))
			->variant(dfa($familyA, 1, 'regular')) # 2015-11-29  E.g.: "regular", "italic", "700", "700italic"
			->preview()->contents()
	;}

	/**
	 * 2015-11-29
	 * @override
	 * @see \Df\Framework\App\Action\Image::type()
	 * @used-by \Df\Framework\App\Action\Image::execute()
	 */
	final protected function type():string {return 'png';}
}