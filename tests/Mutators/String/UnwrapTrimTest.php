<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapTrim;
use PhpParser\Node\ArgPlaceholder;

it('unwraps the trim function', function (): void {
    expect(mutateCode(UnwrapTrim::class, <<<'CODE'
        <?php

        $a = trim('foo ');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo ';
        CODE);
});

it('replaces a trim used as a first class callable with an empty callable', function (): void {
    expect(mutateCode(UnwrapTrim::class, <<<'CODE'
        <?php
        
        array_map(trim(...), $array);
        CODE))->toBe(<<<'CODE'
        <?php
        
        array_map(fn($value) => $value, $array);
        CODE);
});

it('replaces a trim argument placeholder with an empty callable', function (): void {
    expect(mutateCode(UnwrapTrim::class, <<<'CODE'
        <?php

        $callback = trim(?);
        CODE))->toBe(<<<'CODE'
        <?php

        $callback = fn($value) => $value;
        CODE);
})->skip(! class_exists(ArgPlaceholder::class), 'Argument placeholders require PHP-Parser 5.9.');
