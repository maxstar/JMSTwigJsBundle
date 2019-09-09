<?php

namespace JMS\TwigJsBundle\Tests\TwigJs\Compiler;

use JMS\TwigJsBundle\TwigJs\Compiler\TransFilterCompiler;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Translator;

class TransFilterCompilerTest extends BaseTestCase
{
    private $translator;

    protected function setUp() {
        parent::setUp();

        $this->translator = new Translator('en');

        $loader = $this->getMockBuilder('Symfony\Component\Translation\Loader\LoaderInterface')
            ->getMockForAbstractClass();

        $loader
            ->expects($this->any())
            ->method('load')
            ->will($this->returnCallback(function ($messages, $locale, $domain) {
                $catalogue = new MessageCatalogue($locale);
                $catalogue->add($messages, $domain);

                return $catalogue;
            }));
        $this->translator->addLoader('my', $loader);

        $this->compiler->addFilterCompiler(new TransFilterCompiler($this->translator));
        $this->env->addExtension(new TranslationExtension($this->translator));
    }

    public function testCompile() {
        $this->compiler->setDefine('locale', 'de');
        $this->addMessages(['foo' => 'bar'], 'messages', 'de');

        $this->assertContains('sb.append("bar");', $this->compile('{{ "foo"|trans|raw }}'));
    }

    public function testCompileWithParameters() {
        $this->compiler->setDefine('locale', 'en');
        $this->addMessages(['remaining' => '%nb% remaining']);

        $this->assertContains(
            'sb.append(twig.filter.replace("%nb% remaining", {"%nb%": tmp_nb}));',
            $this->compile('{{ "remaining"|trans({"%nb%": nb})|raw }}')
        );
    }

    public function testCompileDynamicTranslations() {
        $this->compiler->setDefine('locale', 'en');

        $this->assertContains('this.env_.filter("trans",', $this->compile(
            '{{ foo|trans }}'));
        $this->assertContains('this.env_.filter("trans",', $this->compile(
            '{{ "foo"|trans({}, bar) }}'));
    }

    public function testCompileWhenNoLocaleIsSet() {
        $this->addMessages(['foo' => 'bar']);
        $this->assertContains('this.env_.filter("trans",', $this->compile(
            '{{ "foo"|trans }}'));
    }

    private function addMessages(array $messages, $domain = 'messages', $locale = 'en') {
        $this->translator->addResource('my', $messages, $locale, $domain);
    }
}