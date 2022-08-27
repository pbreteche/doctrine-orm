<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping as AnnotationNamespace;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

use function sys_get_temp_dir;

class ORMSetupTest extends TestCase
{
    public function testAnnotationConfiguration(): void
    {
        $config = ORMSetup::createAnnotationMetadataConfiguration([], true);

        self::assertInstanceOf(Configuration::class, $config);
        self::assertEquals(sys_get_temp_dir(), $config->getProxyDir());
        self::assertEquals('DoctrineProxies', $config->getProxyNamespace());
        self::assertInstanceOf(AnnotationDriver::class, $config->getMetadataDriverImpl());
    }

    public function testNewDefaultAnnotationDriver(): void
    {
        $paths           = [__DIR__];
        $reflectionClass = new ReflectionClass(AnnotatedDummy::class);

        $annotationDriver = ORMSetup::createDefaultAnnotationDriver($paths);
        $reader           = $annotationDriver->getReader();
        $annotation       = $reader->getMethodAnnotation(
            $reflectionClass->getMethod('namespacedAnnotationMethod'),
            AnnotationNamespace\PrePersist::class,
        );
        self::assertInstanceOf(AnnotationNamespace\PrePersist::class, $annotation);
    }

    public function testAttributeConfiguration(): void
    {
        $config = ORMSetup::createAttributeMetadataConfiguration([], true);

        self::assertInstanceOf(Configuration::class, $config);
        self::assertEquals(sys_get_temp_dir(), $config->getProxyDir());
        self::assertEquals('DoctrineProxies', $config->getProxyNamespace());
        self::assertInstanceOf(AttributeDriver::class, $config->getMetadataDriverImpl());
    }

    public function testXMLConfiguration(): void
    {
        $config = ORMSetup::createXMLMetadataConfiguration([], true);

        self::assertInstanceOf(Configuration::class, $config);
        self::assertInstanceOf(XmlDriver::class, $config->getMetadataDriverImpl());
    }

    /** @requires extension apcu */
    public function testCacheNamespaceShouldBeGeneratedForApcu(): void
    {
        $config = ORMSetup::createConfiguration(false, '/foo');
        $cache  = $config->getMetadataCache();

        $namespaceProperty = new ReflectionProperty(AbstractAdapter::class, 'namespace');
        $namespaceProperty->setAccessible(true);

        self::assertInstanceOf(ApcuAdapter::class, $cache);
        self::assertSame('dc2_1effb2475fcfba4f9e8b8a1dbc8f3caf:', $namespaceProperty->getValue($cache));
    }

    /** @group DDC-1350 */
    public function testConfigureProxyDir(): void
    {
        $config = ORMSetup::createAnnotationMetadataConfiguration([], true, '/foo');
        self::assertEquals('/foo', $config->getProxyDir());
    }

    /** @group DDC-1350 */
    public function testConfigureCache(): void
    {
        $cache  = new ArrayAdapter();
        $config = ORMSetup::createAnnotationMetadataConfiguration([], true, null, $cache);

        self::assertSame($cache, $config->getResultCache());
        self::assertSame($cache, $config->getQueryCache());
        self::assertSame($cache, $config->getMetadataCache());
    }

    /** @group DDC-3190 */
    public function testConfigureCacheCustomInstance(): void
    {
        $cache  = new ArrayAdapter();
        $config = ORMSetup::createConfiguration(true, null, $cache);

        self::assertSame($cache, $config->getResultCache());
        self::assertSame($cache, $config->getQueryCache());
        self::assertSame($cache, $config->getMetadataCache());
    }
}

class AnnotatedDummy
{
    /** @AnnotationNamespace\PrePersist */
    public function namespacedAnnotationMethod(): void
    {
    }
}
