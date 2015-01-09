<?php

namespace SS6\ShopBundle\Tests\Model\Product\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;

class ProductPriceCalculationForUserTest extends PHPUnit_Framework_TestCase {

	public function testCalculatePriceByUserAndDomainIdWithUser() {
		$product = $this->getMock(Product::class, [], [], '', false);
		$pricingGroup = new PricingGroup(new PricingGroupData('name', 1), 1);
		$billingAddress = $this->getMock(BillingAddress::class, [], [], '', false);
		$userData = new UserData();
		$userData->pricingGroup = $pricingGroup;
		$user = new User($userData, $billingAddress, null);
		$expectedProductPrice = new Price(1, 1, 1);

		$currentCustomerMock = $this->getMock(CurrentCustomer::class, [], [], '', false);
		$pricingGroupFacadeMock = $this->getMock(PricingGroupFacade::class, [], [], '', false);

		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
		$productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($expectedProductPrice);

		$productPriceCalculationForUser = new ProductPriceCalculationForUser(
			$productPriceCalculationMock,
			$currentCustomerMock,
			$pricingGroupFacadeMock
		);

		$productPrice = $productPriceCalculationForUser->calculatePriceForUserAndDomainId($product, 1, $user);
		$this->assertEquals($expectedProductPrice, $productPrice);
	}

	public function testCalculatePriceByUserAndDomainIdWithoutUser() {
		$domainId = 1;
		$product = $this->getMock(Product::class, [], [], '', false);
		$pricingGroup = new PricingGroup(new PricingGroupData('name', 1), $domainId);
		$expectedProductPrice = new Price(1, 1, 1);

		$currentCustomerMock = $this->getMock(CurrentCustomer::class, [], [], '', false);

		$pricingGroupFacadeMock = $this->getMock(PricingGroupFacade::class, ['getDefaultPricingGroupByDomainId'], [], '', false);
		$pricingGroupFacadeMock
			->expects($this->once())
			->method('getDefaultPricingGroupByDomainId')
			->with($this->equalTo($domainId))
			->willReturn($pricingGroup);

		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
		$productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($expectedProductPrice);

		$productPriceCalculationForUser = new ProductPriceCalculationForUser(
			$productPriceCalculationMock,
			$currentCustomerMock,
			$pricingGroupFacadeMock
		);

		$productPrice = $productPriceCalculationForUser->calculatePriceForUserAndDomainId($product, $domainId, null);
		$this->assertEquals($expectedProductPrice, $productPrice);
	}
}