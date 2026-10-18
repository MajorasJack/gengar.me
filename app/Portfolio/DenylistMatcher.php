<?php

declare(strict_types=1);

namespace App\Portfolio;

use Illuminate\Support\Collection;

/**
 * Finds denylisted names (CONT-6) in rendered HTML. The denylist holds SHA-256
 * hashes rather than names, so the public repository does not spell out the names
 * the site hides.
 */
final readonly class DenylistMatcher
{
    /** The longest name, in words, that matches() can find. Six leaves room for a four-word name an apostrophe splits. */
    public const int MAX_WORDS = 6;

    /**
     * @param  list<string>  $hashes  lowercase hex SHA-256 hashes of normalised names
     */
    public function __construct(private array $hashes) {}

    /**
     * Lowercase the text and reduce it to its words, joined by single spaces. A word
     * is a run of letters and digits, or a lone `&`, so `Foo&Bar.` becomes `foo & bar`.
     */
    public static function normalise(string $text): string
    {
        preg_match_all('/[\p{L}\p{N}]+|&/u', mb_strtolower($text), $words);

        return implode(' ', $words[0]);
    }

    /**
     * The denylist entry for a name: the SHA-256 hash of its normalised form.
     */
    public static function hash(string $name): string
    {
        return hash('sha256', self::normalise($name));
    }

    /**
     * Return the denylist hashes whose names appear in the HTML, never the names.
     * The page text (tags replaced by spaces) is checked so a name split across
     * tags is found, and the raw markup is checked so a name in an attribute is too.
     *
     * @return list<string>
     */
    public function matches(string $html): array
    {
        return array_values(array_intersect(
            $this->hashes,
            [...self::phraseHashes((string) preg_replace('/<[^>]*>/', ' ', $html)), ...self::phraseHashes($html)],
        ));
    }

    /**
     * Hash every run of one to MAX_WORDS consecutive words in the decoded HTML.
     *
     * @return array<int, string>
     */
    private static function phraseHashes(string $html): array
    {
        $words = collect(explode(' ', self::normalise(html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8'))));

        $hashes = [];

        for ($length = 1; $length <= self::MAX_WORDS; $length++) {
            array_push($hashes, ...$words->sliding($length)->map(fn (Collection $phrase): string => hash('sha256', $phrase->implode(' ')))->values());
        }

        return $hashes;
    }
}
