@forelse ($recentMessages as $message)
    <a href="{{ route('contacts.show', $message) }}" class="ob-list-row">
        <span class="ob-avatar-initials">{{ strtoupper(mb_substr($message->name, 0, 1)) }}</span>
        <div class="ob-list-main">
            <strong>
                {{ $message->name }}
                @if ($message->status === 'new')
                    <span class="badge bg-label-primary ms-1">New</span>
                @endif
            </strong>
            <span>{{ $message->subject ?: \Illuminate\Support\Str::limit($message->message, 70) }}</span>
        </div>
        <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans(null, true) }}</time>
    </a>
@empty
    <div class="ob-empty">
        <div class="ob-empty-icon"><i class="bx bx-envelope-open"></i></div>
        <h6>No messages yet</h6>
        <p>Messages sent through your portfolio's contact form land here.</p>
    </div>
@endforelse
