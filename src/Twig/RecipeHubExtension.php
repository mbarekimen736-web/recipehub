<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RecipeHubExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', $this->timeAgo(...)),
            new TwigFilter('cooking_time_format', $this->cookingTimeFormat(...)),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('difficulty_stars', $this->difficultyStars(...)),
        ];
    }

    /**
     * Filtre time_ago : Convertit une date en format relatif
     * Exemple: "il y a 3 jours", "il y a 2 mois"
     */
    public function timeAgo(\DateTimeInterface $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);

        if ($diff->y > 0) {
            return 'il y a ' . $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        }
        if ($diff->m > 0) {
            return 'il y a ' . $diff->m . ' mois';
        }
        if ($diff->d > 0) {
            return 'il y a ' . $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        }
        if ($diff->h > 0) {
            return 'il y a ' . $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
        }
        if ($diff->i > 0) {
            return 'il y a ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }
        return 'à l\'instant';
    }

    /**
     * Filtre cooking_time_format : Formate le temps de cuisson
     * Exemple: 90 → "1h30", 45 → "45min", 120 → "2h"
     */
    public function cookingTimeFormat(?int $minutes): string
    {
        if (!$minutes) {
            return '0min';
        }
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0 && $mins > 0) {
            return $hours . 'h' . $mins;
        }
        if ($hours > 0) {
            return $hours . 'h';
        }
        return $mins . 'min';
    }

    /**
     * Fonction difficulty_stars : Génère des étoiles pour la difficulté
     * Exemple: facile → ⭐, moyen → ⭐⭐, difficile → ⭐⭐⭐
     */
    public function difficultyStars(string $difficulte): string
    {
        $stars = [
            'facile' => '⭐',
            'moyen' => '⭐⭐',
            'difficile' => '⭐⭐⭐'
        ];
        
        return $stars[$difficulte] ?? '⭐';
    }
}