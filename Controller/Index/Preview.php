<?php
namespace Df\GoogleFont\Controller\Index;
use Df\GoogleFont\Font;
use Df\GoogleFont\Font\Variant;
use Df\GoogleFont\Font\Variant\Preview as VariantPreview;
// 2016-02-17
// Иначе bin/magento setup:di:compile почему-то выдаёт сбой:
// Cannot use Df\GoogleFont\Fonts as Fonts because the name is already in use
// app/code/Df/Api/Controller/Google/FontPreview.php on line 6
use Df\GoogleFont\Fonts as _Fonts;
class Preview extends \Df\Framework\App\Action\Image {
	/**
	 * 2015-11-29
	 * @override
	 * @see \Df\Framework\App\Action\Image::contents()
	 * @return string
	 */
	protected function contents() {return $this->preview()->contents();}

	/**
	 * 2015-11-29
	 * @override
	 * @see \Df\Framework\App\Action\Image::type()
	 * @return string
	 */
	protected function type() {return 'png';}

	/** @return string */
	private function family() {return df_first($this->familyA());}

	/** @return string[] */
	private function familyA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = explode(':', df_request('family'));
		}
		return $this->{__METHOD__};
	}

	/** @return Font */
	private function font() {return _Fonts::s()->get($this->family());}

	/** @return VariantPreview */
	private function preview() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->variant()->preview();
		}
		return $this->{__METHOD__};
	}

	/** @return Variant */
	private function variant() {return $this->font()->variant($this->variantName());}

	/**
	 * 2015-11-29
	 * Например: "regular", "italic", "700", "700italic"
	 * @return string
	 */
	private function variantName() {return dfa($this->familyA(), 1, 'regular');}
}