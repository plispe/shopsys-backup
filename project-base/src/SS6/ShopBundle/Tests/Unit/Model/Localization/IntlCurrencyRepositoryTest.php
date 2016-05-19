<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Localization;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Localization\IntlCurrencyRepository;

/**
 * @UglyTest
 */
class IntlCurrencyRepositoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getSupportedCurrencyCodes
	 */
	public function testGetSupportedCurrencies($currencyCode) {
		$intlCurrencyRepository = new IntlCurrencyRepository();
		$intlCurrencyRepository->get($currencyCode);
	}

	/**
	 * @return string[][]
	 */
	public function getSupportedCurrencyCodes() {
		$data = [];
		foreach (IntlCurrencyRepository::SUPPORTED_CURRENCY_CODES as $currencyCode) {
			$data[] = ['currencyCode' => $currencyCode];
		}

		return $data;
	}

}
