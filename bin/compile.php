<?php

use JMS\TwigJsBundle\Command\CompileTwigCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\ErrorHandler\Debug;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use TwigJs\CompileRequestHandler;
use TwigJs\JsCompiler;
use TwigJs\Twig\TwigJsExtension;

require_once __DIR__ . '/../vendor/autoload.php';

$_SERVER['argv'] = array_merge(array_slice($_SERVER['argv'], 0, 1), ['jms:twig:compile'], array_slice($_SERVER['argv'], 1));

$input = new ArgvInput();
//$env = $input->getParameterOption(array('--env', '-e'), getenv('SYMFONY_ENV') ?: 'dev');
//
//// we use env=prod without --no-debug, i.e. in debug mode to dump assetic
//$debug = getenv('SYMFONY_DEBUG') !== '0' && !$input->hasParameterOption(array('--no-debug', '')); // && $env !== 'prod';
//
//if ($debug) {
//    Debug::enable();
//}

$application = new Application();
$environment = new Environment(
    new FilesystemLoader([
        FilesystemLoader::MAIN_NAMESPACE => dirname(__DIR__, 1)
    ])
);
$environment->addExtension(new TwigJsExtension());

$application->add(
    new CompileTwigCommand(
        'jms:twig:compile',
        new CompileRequestHandler(
            $environment,
            new JsCompiler($environment)
        )
    )
);

$application->run($input);