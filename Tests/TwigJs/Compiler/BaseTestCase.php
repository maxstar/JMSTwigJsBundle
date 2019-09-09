<?php

namespace JMS\TwigJsBundle\Tests\TwigJs\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\TwigBundle\Extension\AssetsExtension;
use TwigJs\JsCompiler;

if (\class_exists('\PHPUnit\Framework\TestCase')) {
    abstract class BaseTestCaseAbstract extends TestCase
    {
    }
} else {
    abstract class BaseTestCaseAbstract extends \PHPUnit_Framework_TestCase
    {
    }
}

abstract class BaseTestCase extends BaseTestCaseAbstract
{
    protected $env;
    protected $compiler;

    protected function compile($source, $name = null) {
        return $this->env->compileSource($source, $name);
    }

    protected function getNodes($source, $name = null) {
        return $this->env->parse($this->env->tokenize($source, $name));
    }

    protected function setUp() {
        $this->env = $env = new \Twig_Environment();
        $env->addExtension(new \Twig_Extension_Core());
        $env->setCompiler($this->compiler = new JsCompiler($env));
    }
}