<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectVersion extends Model
{
    use HasFactory;

    protected $table = 'project_versions';

    protected $fillable = [
        'project_id',
        'version_number',
        'data',
        'changed_by',
        'change_notes',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Methods
    public function restore()
    {
        $project = $this->project;
        $data = $this->data;

        // Remove non-fillable fields
        unset($data['id'], $data['created_at'], $data['updated_at']);

        $project->update($data);

        return $project;
    }
}
