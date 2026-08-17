<?php

namespace App\Notifications\Messages;

class WebPushMessage
{
    public string $title = '';

    public string $body = '';

    public ?string $url = null;

    public ?string $icon = null;

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function body(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url ?? '/',
            'icon' => $this->icon ?? '/images/favicon.png',
        ];
    }
}
