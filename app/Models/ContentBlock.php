<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentBlock extends Model
{
    protected $guarded = [];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function dialogueLines(): HasMany
    {
        return $this->hasMany(DialogueLine::class)->orderBy('sort');
    }

    public function vocabWords(): HasMany
    {
        return $this->hasMany(VocabWord::class)->orderBy('sort');
    }
}
