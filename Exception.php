<?php
namespace Df\GoogleFont;
/**
 * 2015-11-17
 * В документации об этом ни слова не сказано,
 * однако в случае сбоя Google API возвращает JSON следующией структуры:
	{
		error: {
			errors: [
				{
					domain: "usageLimits",
					reason: "accessNotConfigured",
					message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration.",
					extendedHelp: "https://console.developers.google.com"
				}
			],
			code: 403,
			message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration."
		}
	}
 * https://developers.google.com/fonts/docs/developer_api
 */
final class Exception extends \Df\Core\Exception {
	/**
	 * 2015-11-27
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @used-by df_xts()
	 */
	function message():string {
		$ra[]= "Google Fonts API error: «{$this['message']}»."; /** @var string[] $ra */
		# 2015-11-28
		#	{
		#		domain: "usageLimits",
		#		reason: "accessNotConfigured",
		#		message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration.",
		#		extendedHelp: "https://console.developers.google.com"
		#	}
		if ('accessNotConfigured' === dfa(df_first($this['errors']), 'reason')) {
			$ra[] = 'You need to setup Google Fonts API using the instruction https://mage2.pro/t/269';
		}
		return df_cc_n($ra);
	}
}