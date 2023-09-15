<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\LocaleBundle\Templating\Helper;

use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use Owl\Component\Locale\Converter\LocaleConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

final class LocaleHelper extends Helper implements LocaleHelperInterface
{
    /** @var LocaleConverterInterface */
    private $localeConverter;

    /** @var LocaleContextInterface|null */
    private $localeContext;

    public function __construct(LocaleConverterInterface $localeConverter, ?LocaleContextInterface $localeContext = null)
    {
        if (null === $localeContext) {
            @trigger_error('Not passing LocaleContextInterface explicitly as the second argument is deprecated since 1.4 and will be prohibited in 2.0', \E_USER_DEPRECATED);
        }

        $this->localeConverter = $localeConverter;
        $this->localeContext = $localeContext;
    }

    /**
     * @return string
     */
    public function convertCodeToName(string $code, ?string $localeCode = null): ?string
    {
        try {
            return $this->localeConverter->convertCodeToName($code, $this->getLocaleCode($localeCode));
        } catch (\InvalidArgumentException $e) {
            return $code;
        }
    }

    /**
     * @psalm-return 'sylius_locale'
     */
    public function getName(): string
    {
        return 'sylius_locale';
    }

    private function getLocaleCode(?string $localeCode): ?string
    {
        if (null !== $localeCode) {
            return $localeCode;
        }

        if (null === $this->localeContext) {
            return null;
        }

        try {
            return $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException $exception) {
            return null;
        }
    }
}
