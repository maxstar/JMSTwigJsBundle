<?php

namespace JMS\TwigJsBundle\Tests\TwigJs\Compiler;

use JMS\TwigJsBundle\TwigJs\Compiler\TransFilterCompiler;
use Twig\Loader\ArrayLoader;
use TwigJs\JsCompiler;
use Symfony\Bundle\TwigBundle\Extension\AssetsExtension;

abstract class BaseTestCase extends TestCase
{
    protected $env;
    protected $compiler;

    protected function compile($source, $name = null)
    {
        return $this->env->compileSource(new \Twig_Source($source, $name));
    }

    protected function getNodes($source, $name = null)
    {
        return $this->env->parse($this->env->tokenize($source, $name));
    }

    protected function setUp()
    {
        $this->env = $env = new \Twig\Environment(new ArrayLoader());
        $env->setCompiler($this->compiler = new JsCompiler($env));
    }
}