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

namespace spec\Owl\Bundle\LocaleBundle\Context;

use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use Owl\Component\Locale\Provider\LocaleProviderInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestBasedLocaleContextSpec extends ObjectBehavior
{
    public function let(RequestStack $requestStack, LocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($requestStack, $localeProvider);
    }

    public function it_is_a_locale_context(): void
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    public function it_throws_locale_not_found_exception_if_master_request_is_not_found(RequestStack $requestStack): void
    {
        $requestStack->getMasterRequest()->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    public function it_throws_locale_not_found_exception_if_master_request_does_not_have_locale_attribute(
        RequestStack $requestStack,
        Request $request,
    ): void {
        $requestStack->getMasterRequest()->willReturn($request);

        $request->attributes = new ParameterBag();

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    public function it_throws_locale_not_found_exception_if_master_request_locale_code_is_not_among_available_ones(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
        Request $request,
    ): void {
        $requestStack->getMasterRequest()->willReturn($request);

        $request->attributes = new ParameterBag(['_locale' => 'en_US']);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    public function it_returns_master_request_locale_code(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
        Request $request,
    ): void {
        $requestStack->getMasterRequest()->willReturn($request);

        $request->attributes = new ParameterBag(['_locale' => 'pl_PL']);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->getLocaleCode()->shouldReturn('pl_PL');
    }
}
