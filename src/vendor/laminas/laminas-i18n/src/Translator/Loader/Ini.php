<?php

namespace Laminas\I18n\Translator\Loader;

use Laminas\Config\Reader\Ini as IniReader;
use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;

use function array_shift;
use function count;
use function gettype;
use function is_array;
use function is_file;
use function is_readable;
use function sprintf;
use function stream_resolve_include_path;

/**
 * PHP INI format loader.
 *
 * @final
 */
class Ini extends AbstractFileLoader
{
    /**
     * load(): defined by FileLoaderInterface.
     *
     * @see    FileLoaderInterface::load()
     *
     * @param  string $locale
     * @param  string $filename
     * @return TextDomain
     * @throws Exception\InvalidArgumentException
     */
    public function load($locale, $filename)
    {
        $resolvedIncludePath = stream_resolve_include_path($filename);
        $fromIncludePath     = $resolvedIncludePath !== false ? $resolvedIncludePath : $filename;
        if (! $fromIncludePath || ! is_file($fromIncludePath) || ! is_readable($fromIncludePath)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not find or open file %s for reading',
                $filename
            ));
        }

        $messages           = [];
        $iniReader          = new IniReader();
        $messagesNamespaced = $iniReader->fromFile($fromIncludePath);

        $list = $messagesNamespaced;
        if (isset($messagesNamespaced['translation'])) {
            $list = $messagesNamespaced['translation'];
        }

        foreach ($list as $message) {
            if (! is_array($message) || count($message) < 2) {
                throw new Exception\InvalidArgumentException(
                    'Each INI row must be an array with message and translation'
                );
            }
            if (isset($message['message'], $message['translation'])) {
                $messages[$message['message']] = $message['translation'];
                continue;
            }
            $messages[array_shift($message)] = array_shift($message);
        }

        if (! is_array($messages)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array, but received %s',
                gettype($messages)
            ));
        }

        $textDomain = new TextDomain($messages);

        if (isset($messagesNamespaced['plural']['plural_forms'])) {
            $textDomain->setPluralRule(
                PluralRule::fromString($messagesNamespaced['plural']['plural_forms'])
            );
        }

        return $textDomain;
    }
}
