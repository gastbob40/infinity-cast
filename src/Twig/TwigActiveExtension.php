<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigActiveExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu_active', [$this, 'menuActive'], ['is_safe' => ['html'], 'needs_context' => true]),
        ];
    }

    /**
     * Ajout une class is-active pour les éléments actifs du menu.
     *
     * @param array<string,mixed> $context
     * @return string
     */
    public function menuActive(array $context, string $name): string
    {
        if (($context['menu'] ?? null) === $name) {
            return ' aria-current="page"';
        }

        return '';
    }
}