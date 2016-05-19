<?php

namespace SS6\ShopBundle\Tests\Unit\Component\UploadedFile;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig;
use SS6\ShopBundle\Component\UploadedFile\UploadedFile;
use SS6\ShopBundle\Component\UploadedFile\UploadedFileDeleteDoctrineListener;
use SS6\ShopBundle\Component\UploadedFile\UploadedFileFacade;
use SS6\ShopBundle\Tests\Unit\Component\UploadedFile\Dummy;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @UglyTest
 */
class UploadedFileDeleteDoctrineListenerTest extends PHPUnit_Framework_TestCase {

	public function testPreRemoveDeleteFile() {
		$uploadedFile = new UploadedFile('entityName', 1, 'dummy.txt');

		$uploadedFileConfig = new UploadedFileConfig([]);

		$uploadedFileFacadeMock = $this->getMock(UploadedFileFacade::class, ['deleteFileFromFilesystem'], [], '', false);
		$uploadedFileFacadeMock->expects($this->once())->method('deleteFileFromFilesystem')->with($this->equalTo($uploadedFile));

		$containerMock = $this->getMockBuilder(ContainerInterface::class)
			->disableOriginalConstructor()
			->setMethods(['get'])
			->getMockForAbstractClass();
		$containerMock->expects($this->once())->method('get')->willReturn($uploadedFileFacadeMock);

		$args = $this->getMock(LifecycleEventArgs::class, ['getEntity'], [], '', false);
		$args->method('getEntity')->willReturn($uploadedFile);

		$doctrineListener = new UploadedFileDeleteDoctrineListener($containerMock, $uploadedFileConfig);
		$doctrineListener->preRemove($args);
	}

	public function testPreRemoveDeleteUploadedFile() {
		$entity = new Dummy();
		$uploadedFile = new UploadedFile('entitzId', 1, 'dummy.txt');

		$uploadedFileEntityConfig = new UploadedFileEntityConfig('entityName', Dummy::class);
		$uploadedFileConfig = new UploadedFileConfig([
			Dummy::class => $uploadedFileEntityConfig,
		]);

		$uploadedFileFacadeMock = $this->getMock(UploadedFileFacade::class, ['getUploadedFileByEntity'], [], '', false);
		$uploadedFileFacadeMock
			->expects($this->once())
			->method('getUploadedFileByEntity')
			->with($this->equalTo($entity))
			->willReturn($uploadedFile);

		$containerMock = $this->getMockBuilder(ContainerInterface::class)
			->disableOriginalConstructor()
			->setMethods(['get'])
			->getMockForAbstractClass();
		$containerMock->expects($this->once())->method('get')->willReturn($uploadedFileFacadeMock);

		$emMock = $this->getMock(EntityManager::class, ['remove'], [], '', false);
		$emMock->expects($this->once())->method('remove')->with($uploadedFile);

		$args = $this->getMock(LifecycleEventArgs::class, ['getEntity', 'getEntityManager'], [], '', false);
		$args->method('getEntity')->willReturn($entity);
		$args->method('getEntityManager')->willReturn($emMock);

		$doctrineListener = new UploadedFileDeleteDoctrineListener($containerMock, $uploadedFileConfig);
		$doctrineListener->preRemove($args);
	}

}
