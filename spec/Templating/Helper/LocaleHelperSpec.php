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

namespace spec\Owl\Bundle\LocaleBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Owl\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface;
use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use Owl\Component\Locale\Converter\LocaleConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

final class LocaleHelperSpec extends ObjectBehavior
{
    public function let(LocaleConverterInterface $localeConverter): void
    {
        $this->beConstructedWith($localeConverter);
    }

    public function it_is_a_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    public function it_is_a_locale_helper(): void
    {
        $this->shouldImplement(LocaleHelperInterface::class);
    }

    public function it_converts_locales_code_to_name(LocaleConverterInterface $localeConverter): void
    {
        $localeConverter->convertCodeToName('fr_FR', null)->willReturn('French (France)');

        $this->convertCodeToName('fr_FR')->shouldReturn('French (France)');
    }

    public function it_converts_locales_code_to_name_using_given_locale(LocaleConverterInterface $localeConverter): void
    {
        $localeConverter->convertCodeToName('en', 'pl')->willReturn('angielski');

        $this->convertCodeToName('en', 'pl')->shouldReturn('angielski');
    }

    public function it_converts_locales_code_to_name_using_locale_from_the_context(
        LocaleConverterInterface $localeConverter,
        LocaleContextInterface $localeContext
    ): void {
        $this->beConstructedWith($localeConverter, $localeContext);

        $localeContext->getLocaleCode()->willReturn('pl');

        $localeConverter->convertCodeToName('en', 'pl')->willReturn('angielski');

        $this->convertCodeToName('en')->shouldReturn('angielski');
    }

    public function it_converts_locale_code_to_name_using_default_locale_if_passed_locale_context_throws_an_exception(
        LocaleConverterInterface $localeConverter,
        LocaleContextInterface $localeContext
    ): void {
        $this->beConstructedWith($localeConverter, $localeContext);

        $localeContext->getLocaleCode()->willThrow(LocaleNotFoundException::class);

        $localeConverter->convertCodeToName('en', null)->willReturn('English');

        $this->convertCodeToName('en')->shouldReturn('English');
    }

    public function it_fallbacks_to_the_code_if_the_name_is_not_in_the_database(LocaleConverterInterface $localeConverter): void
    {
        $localeConverter->convertCodeToName('en_DG', null)->willThrow(new \InvalidArgumentException());

        $this->convertCodeToName('en_DG')->shouldReturn('en_DG');
    }

    public function it_has_a_name(): void
    {
        $this->getName()->shouldReturn('sylius_locale');
    }
}
