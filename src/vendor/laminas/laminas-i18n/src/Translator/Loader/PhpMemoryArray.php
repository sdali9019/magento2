<?php

namespace Laminas\I18n\Translator\Loader;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;

use function gettype;
use function is_array;
use function sprintf;

/**
 * PHP Memory array loader.
 *
 * @final
 */
class PhpMemoryArray implements RemoteLoaderInterface
{
    /** @param array $messages */
    public function __construct(protected $messages)
    {
    }

    /**
     * Load translations from a remote source.
     *
     * @param  string $locale
     * @param  string $textDomain
     * @return TextDomain
     * @throws Exception\InvalidArgumentException
     */
    public function load($locale, $textDomain)
    {
        if (! is_array($this->messages)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Expected an array, but received %s', gettype($this->messages))
            );
        }

        if (! isset($this->messages[$textDomain])) {
            throw new Exception\InvalidArgumentException(
                sprintf('Expected textdomain "%s" to be an array, but it is not set', $textDomain)
            );
        }

        if (! isset($this->messages[$textDomain][$locale])) {
            throw new Exception\InvalidArgumentException(
                sprintf('Expected locale "%s" to be an array, but it is not set', $locale)
            );
        }

        $textDomain = new TextDomain($this->messages[$textDomain][$locale]);

        if ($textDomain->offsetExists('')) {
            if (isset($textDomain['']['plural_forms'])) {
                $textDomain->setPluralRule(
                    PluralRule::fromString($textDomain['']['plural_forms'])
                );
            }

            unset($textDomain['']);
        }

        return $textDomain;
    }
}
