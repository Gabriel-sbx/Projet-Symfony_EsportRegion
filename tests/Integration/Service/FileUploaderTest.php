<?php

namespace App\Tests\Integration\Service;

use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileUploaderTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        $fileUploader = static::getContainer()->get(FileUploader::class);
        $fileUploader->uploadFile('testFileUploader.html');
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }
}
