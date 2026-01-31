<?php

namespace App\Policies;

use App\Models\Band;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BandPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Band $band)
    {
        $member = $band->members()->where('user_id', $user->id)->first();
        return $member !== null;
    }

    public function update(User $user, Band $band)
    {
        $member = $band->members()->where('user_id', $user->id)->first();
        
        \Log::info('Verificando autorização para atualizar banda', [
            'user_id' => $user->id,
            'band_id' => $band->id,
            'member' => $member,
            'is_authorized' => $member && ($member->role === 'admin' || $member->role === 'editor')
        ]);
        
        return $member && ($member->role === 'admin' || $member->role === 'editor');
    }

    public function delete(User $user, Band $band)
    {
        $member = $band->members()->where('user_id', $user->id)->first();
        return $member && $member->role === 'admin';
    }

    public function transfer(User $user, Band $band)
    {
        $member = $band->members()->where('user_id', $user->id)->first();
        return $member && $member->role === 'admin';
    }

    public function manageMembers(User $user, Band $band)
    {
        $member = $band->members()->where('user_id', $user->id)->first();
        return $member && $member->role === 'admin';
    }

    public function createEvent(User $user, Band $band)
    {
        $member = $band->members()->where('user_id', $user->id)->first();
        
        \Log::info('Verificando autorização para criar evento', [
            'user_id' => $user->id,
            'band_id' => $band->id,
            'member' => $member,
            'is_authorized' => $member && in_array($member->role, ['admin', 'editor'])
        ]);
        
        return $member && in_array($member->role, ['admin', 'editor']);
    }

    public function before(User $user, $ability, $band = null)
    {
        if ($band) {
            $member = $band->members()->where('user_id', $user->id)->first();
            if ($member && $member->role === 'admin') {
                return true;
            }
        }
        return null;
    }
} 