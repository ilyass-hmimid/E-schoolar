<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'status_code',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Enregistrer une action dans les logs
     */
    public static function log(
        string $action,
        string $description,
        ?User $user = null,
        array $metadata = []
    ): self {
        return self::create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()?->ip() ?? '127.0.0.1',
            'user_agent' => request()?->userAgent() ?? 'console',
            'status_code' => 200, // Par défaut, sera mis à jour par le middleware si nécessaire
        ]);
    }

    /**
     * Filtrer les paramètres sensibles avant enregistrement
     */
    public static function filterParameters(array $parameters): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'cvv',
        ];

        foreach ($sensitiveKeys as $key) {
            if (array_key_exists($key, $parameters)) {
                $parameters[$key] = '********';
            }
        }

        return $parameters;
    }
}
