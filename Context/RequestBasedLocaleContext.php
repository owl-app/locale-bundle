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

namespace Owl\Bundle\LocaleBundle\Context;

use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use Owl\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestBasedLocaleContext implements LocaleContextInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var LocaleProviderInterface */
    private $localeProvider;

    public function __construct(RequestStack $requestStack, LocaleProviderInterface $localeProvider)
    {
        $this->requestStack = $requestStack;
        $this->localeProvider = $localeProvider;
    }

    public function getLocaleCode(): string
    {
        if (\method_exists($this->requestStack, 'getMainRequest')) {
            $request = $this->requestStack->getMainRequest();
        } else {
            $request = $this->requestStack->getMasterRequest();
        }
        if (null === $request) {
            throw new LocaleNotFoundException('No master request available.');
        }

        $localeCode = $request->attributes->get('_locale');
        if (null === $localeCode) {
            throw new LocaleNotFoundException('No locale attribute is set on the master request.');
        }

        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
        if (!in_array($localeCode, $availableLocalesCodes, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}
