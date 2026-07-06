<?php

namespace App\Support;

class NombreEnLettres
{
    private static array $unites = [
        '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
        'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize',
        'dix-sept', 'dix-huit', 'dix-neuf',
    ];

    private static array $dizaines = [
        0 => '', 2 => 'vingt', 3 => 'trente', 4 => 'quarante',
        5 => 'cinquante', 6 => 'soixante', 7 => 'soixante', 8 => 'quatre-vingt', 9 => 'quatre-vingt',
    ];

    /**
     * Convertit un entier en toutes lettres françaises et l'accompagne de "francs CFA".
     */
    public static function enMontant(float|int $nombre): string
    {
        $entier = (int) round($nombre);

        if ($entier === 0) {
            return 'zéro franc CFA';
        }

        $lettres = self::convertir($entier);
        $lettres = ucfirst($lettres);

        return $lettres . ' franc' . ($entier > 1 ? 's' : '') . ' CFA';
    }

    /**
     * Convertit un entier positif en toutes lettres françaises (sans unité monétaire).
     */
    public static function convertir(int $nombre): string
    {
        if ($nombre === 0) {
            return 'zéro';
        }

        if ($nombre < 0) {
            return 'moins ' . self::convertir(abs($nombre));
        }

        $milliards = intdiv($nombre, 1_000_000_000);
        $reste     = $nombre % 1_000_000_000;
        $millions  = intdiv($reste, 1_000_000);
        $reste     = $reste % 1_000_000;
        $milliers  = intdiv($reste, 1_000);
        $reste     = $reste % 1_000;

        $mots = [];

        if ($milliards > 0) {
            $mots[] = self::centaines($milliards, true) . ' milliard' . ($milliards > 1 ? 's' : '');
        }

        if ($millions > 0) {
            $mots[] = self::centaines($millions, true) . ' million' . ($millions > 1 ? 's' : '');
        }

        if ($milliers > 0) {
            // "cent" et "quatre-vingt" ne prennent jamais de 's' juste avant "mille"
            $mots[] = $milliers === 1 ? 'mille' : self::centaines($milliers, false) . ' mille';
        }

        if ($reste > 0) {
            $mots[] = self::centaines($reste, true);
        }

        return implode(' ', $mots);
    }

    /**
     * Convertit un nombre de 0 à 999 en lettres.
     * $terminal = false si ce segment est immédiatement suivi de "mille" (auquel cas
     * "cent" et "quatre-vingt" ne prennent jamais de 's').
     */
    private static function centaines(int $n, bool $terminal = true): string
    {
        if ($n < 20) {
            return self::$unites[$n];
        }

        if ($n < 100) {
            return self::dizainesUnites($n, $terminal);
        }

        $centaines = intdiv($n, 100);
        $reste     = $n % 100;

        $mot = ($centaines === 1 ? 'cent' : self::$unites[$centaines] . ' cent');

        if ($reste === 0) {
            if ($centaines > 1 && $terminal) {
                $mot .= 's'; // "deux cents" mais "deux cent mille" / "deux cent trois"
            }
        } else {
            $mot .= ' ' . self::dizainesUnites($reste, $terminal);
        }

        return $mot;
    }

    /**
     * Convertit un nombre de 0 à 99 en lettres (règles françaises : soixante-dix, quatre-vingt-dix...).
     */
    private static function dizainesUnites(int $n, bool $terminal = true): string
    {
        if ($n < 20) {
            return self::$unites[$n];
        }

        $d = intdiv($n, 10);
        $u = $n % 10;

        // 70-79 -> soixante + (10-19), 90-99 -> quatre-vingt + (10-19)
        if ($d === 7 || $d === 9) {
            return self::$dizaines[$d] . '-' . self::$unites[10 + $u];
        }

        $mot = self::$dizaines[$d];

        if ($u === 0) {
            // "quatre-vingts" prend un 's' sauf juste avant "mille"
            return ($d === 8 && $terminal) ? $mot . 's' : $mot;
        }

        if ($u === 1 && $d !== 8) {
            return $mot . '-et-un';
        }

        return $mot . '-' . self::$unites[$u];
    }
}