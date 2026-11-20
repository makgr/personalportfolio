@php
    $statusBadge = [
        'published' => 'bg-label-success',
        'draft' => 'bg-label-secondary',
        'archived' => 'bg-label-warning',
    ];
@endphp
@if ($recentProjects->isEmpty())
    <div class="ob-empty">
        <div class="ob-empty-icon"><i class="bx bx-folder"></i></div>
        <h6>No projects yet</h6>
        <p>Add your first project so it shows up on your portfolio.</p>
        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm"><i class="bx bx-plus"></i> Add project</a>
    </div>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th class="text-end">Added</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentProjects as $project)
                    <tr>
                        <td>
                            <a href="{{ route('projects.show', $project) }}" class="d-flex align-items-center gap-3 text-body">
                                @if ($project->featured_image)
                                    <img src="{{ asset($project->featured_image) }}" alt="" class="ob-thumb" loading="lazy">
                                @else
                                    <span class="ob-thumb"></span>
                                @endif
                                <strong class="text-truncate" style="max-width: 16rem">{{ $project->title }}</strong>
                            </a>
                        </td>
                        <td>{{ $project->category->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $statusBadge[$project->status] ?? 'bg-label-secondary' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td class="text-end text-muted small">{{ $project->created_at->format('j M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
