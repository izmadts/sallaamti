<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

// A stripped-down shell (logo header, no nav, no footer nav/newsletter, no
// bottom tab bar) for one-task pages someone reaches via a signed link sent
// directly to them — e.g. a matchmaking client's progress page — rather
// than a page they're meant to browse the wider site from. See
// resources/views/layouts/minimal.blade.php for the reasoning.
class MinimalLayout extends Component
{
    public function __construct(
        public ?string $title = null,
    ) {}

    public function render(): View
    {
        return view('layouts.minimal');
    }
}
