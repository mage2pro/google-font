<?php
namespace Dfe\GoogleFont;
# 2015-11-17
# В документации об этом ни слова не сказано, однако в случае сбоя Google API возвращает JSON следующией структуры:
#	{
#		error: {
#			errors: [
#				{
#					domain: "usageLimits",
#					reason: "accessNotConfigured",
#					message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project.Please use the Google Developers Console to update your configuration.",
#				extendedHelp: "https://console.developers.google.com"
#				}
#			],
#			code: 403,
#			message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration."
#		}
#	}
# https://developers.google.com/fonts/docs/developer_api
/** @used-by \Dfe\GoogleFont\Fonts::responseA() */
final class ResponseValidator extends \Df\API\Response\Validator {
	/**
	 * 2015-11-27
	 * @override
	 * @see \Df\API\Exception::short()
	 * @used-by \Df\API\Client::_p()
	 * @used-by \Df\API\Exception::message()
	 */
	function short():string {return df_cc_n("Google Fonts API error: «{$this->r('message')}».",
		# 2015-11-28
		#	{
		#		domain: "usageLimits",
		#		reason: "accessNotConfigured",
		#		message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration.",
		#		extendedHelp: "https://console.developers.google.com"
		#	}
		'accessNotConfigured' !== dfa(df_first($this['errors']), 'reason') ? '' :
			"You need to setup the Google Fonts' API using the instruction https://mage2.pro/t/269"
	);}

	/**
	 * 2024-05-22 "Remove `Df\Core\Exception::$_data`": https://github.com/mage2pro/core/issues/385
	 * @override
	 * @see \Df\API\Response\Validator::valid()
	 * @used-by \Df\API\Client::_p()
	 */
	function valid():bool {return !$this->r();}
}