<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    protected $fillable = [
        'employe_id',
        'date',
        'heure_arrivee',
        'heure_depart',
        'statut',
        'type',
        'heures_travaillees',
        'salaire_jour',
        'retard',
        'minutes_retard',
        'created_by',
    ];

    protected $casts = [
        'date'    => 'date',
        'retard'  => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Constantes métier
    |--------------------------------------------------------------------------
    */
    const HEURE_ARRIVEE_PREVUE  = '08:45:00'; // heure d'arrivée prévue
    const HEURE_DEPART_PREVUE   = '18:00:00'; // heure de départ prévue
    const TOLERANCE_RETARD_MIN  = 0;          // tolérance retard en minutes
    const HEURES_JOURNEE        = 9;           // heures de travail prévues/jour
    const JOURS_OUVRABLES_MOIS  = 26;          // pour calculer salaire journalier

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Logique métier — Calculs automatiques
    |--------------------------------------------------------------------------
    */

    /**
     * Calculer et appliquer le statut retard à l'arrivée
     */
    public function calculerRetard(): void
    {
        if (!$this->heure_arrivee) return;

        $arrivee       = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->heure_arrivee);
        $arrivePrevue  = Carbon::parse($this->date->format('Y-m-d') . ' ' . self::HEURE_ARRIVEE_PREVUE);
        $limiteRetard  = $arrivePrevue->copy()->addMinutes(self::TOLERANCE_RETARD_MIN);

        if ($arrivee->gt($limiteRetard)) {
            $this->retard         = true;
            $this->minutes_retard = $arrivee->diffInMinutes($arrivePrevue);
            $this->statut         = 'retard';
        } else {
            $this->retard         = false;
            $this->minutes_retard = 0;
            $this->statut         = 'present';
        }
    }

    /**
     * Calculer les heures travaillées et le salaire journalier au départ
     */
    public function calculerHeuresEtSalaire(): void
    {
        if (!$this->heure_arrivee || !$this->heure_depart) return;

        $arrivee = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->heure_arrivee);
        $depart  = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->heure_depart);

        // Heures travaillées en décimal (ex: 8.75 pour 8h45)
        $minutesTravaillees      = $arrivee->diffInMinutes($depart);
        $this->heures_travaillees = round($minutesTravaillees / 60, 2);

        // Salaire journalier
        if ($this->employe && $this->employe->salaire_jour) {
            $this->salaire_jour   = $this->employe->salaire_jour;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Durée travaillée formatée (ex: "8h45")
     */
    public function getDureeFormatteeAttribute(): string
    {
        if (!$this->heures_travaillees) return '—';

        $heures  = (int) $this->heures_travaillees;
        $minutes = round(($this->heures_travaillees - $heures) * 60);

        return $minutes > 0 ? "{$heures}h{$minutes}" : "{$heures}h00";
    }

    /**
     * Retard formaté (ex: "23 min")
     */
    public function getRetardFormateAttribute(): string
    {
        if (!$this->retard || !$this->minutes_retard) return '—';
        return "{$this->minutes_retard} min";
    }

    /**
     * Badge couleur selon statut
     */
    public function getBadgeStatutAttribute(): array
    {
        return match($this->statut) {
            'present' => ['label' => 'Présent',  'bg' => '#EAF3DE', 'color' => '#3B6D11'],
            'retard'  => ['label' => 'Retard',   'bg' => '#FEF6E4', 'color' => '#92400E'],
            'absent'  => ['label' => 'Absent',   'bg' => '#FCEBEB', 'color' => '#A32D2D'],
            default   => ['label' => 'Inconnu',  'bg' => '#F1F5F9', 'color' => '#64748B'],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeCeMois($query)
    {
        return $query->whereMonth('date', now()->month)
                     ->whereYear('date', now()->year);
    }

    public function scopeCetteAnnee($query)
    {
        return $query->whereYear('date', now()->year);
    }

    public function scopePresents($query)
    {
        return $query->whereIn('statut', ['present', 'retard']);
    }

    public function scopeAbsents($query)
    {
        return $query->where('statut', 'absent');
    }
}