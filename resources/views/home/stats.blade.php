@php
    $cards = [
        ['label' => 'Projects', 'icon' => 'bx-folder', 'value' => $stats['projects'],
            'meta' => $stats['projectsPublished'] . ' published', 'url' => route('projects.index')],
        ['label' => 'Skills', 'icon' => 'bx-code-alt', 'value' => $stats['skills'],
            'meta' => 'Shown on your portfolio', 'url' => route('skills.index')],
        ['label' => 'Messages', 'icon' => 'bx-envelope', 'value' => $stats['messages'],
            'meta' => $stats['messagesNew'] ? $stats['messagesNew'] . ' new' : 'No new messages', 'url' => route('contacts.index')],
        ['label' => 'Blog posts', 'icon' => 'bx-edit', 'value' => $stats['blogPosts'],
            'meta' => $stats['blogPostsPublished'] . ' published', 'url' => route('blog-posts.index')],
        ['label' => 'Visitors', 'icon' => 'bx-ob-chart', 'value' => $stats['visitors'],
            'meta' => 'Unique, last 30 days', 'url' => null],
    ];
@endphp
@foreach ($cards as $card)
    <div class="col">
        @if ($card['url'])
            <a href="{{ $card['url'] }}" class="ob-stat-link">
        @endif
        <div class="card h-100">
            <div class="ob-stat">
                <div class="ob-stat-top">
                    <span>{{ $card['label'] }}</span>
                    <span class="ob-stat-icon"><i class="bx {{ $card['icon'] }}"></i></span>
                </div>
                <div class="ob-stat-value">{{ number_format($card['value']) }}</div>
                <div class="ob-stat-meta">{{ $card['meta'] }}</div>
            </div>
        </div>
        @if ($card['url'])
            </a>
        @endif
    </div>
@endforeach
