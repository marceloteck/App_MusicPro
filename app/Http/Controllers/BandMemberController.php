<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Models\Playlist;
use App\Models\User;
use App\Models\BandMember;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;


class BandMemberController extends Controller
{
    public function index($id)
    {
        $user = auth()->user();

        // Busca a banda que o usuário participa
        $band = Band::where('id', $id)
            ->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['members.user', 'events'])
            ->first();

        // Se não encontrou, significa que o user não faz parte
        if (!$band) {
           $band = null;

           return Inertia::render('Pages/Bands/Show', [
            'band' => $band,
            'message' => 'Você não faz parte dessa banda.',
        ]);

        }

        return Inertia::render('Pages/Bands/Show', [
            'band' => $band,
            'events' => $band->events()->orderBy('date')->get(),
            'userLists' => Playlist::where('user_id', $user->id)->with('songs')->get(),
            'flash' => session('flash'),
        ]);
    }
   

    public function store(Request $request, Band $band)
    {
        try {
            $this->authorize('manageMembers', $band);

            $request->validate([
                'email' => 'required|email|exists:users,email',
                'role' => 'required|in:editor,viewer'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors([
                    'flash' => [
                        'error' => 'Usuário não encontrado.'
                    ]
                ]);
            }

            if ($band->members()->where('user_id', $user->id)->exists()) {
                return back()->withErrors([
                    'flash' => [
                        'error' => 'Este usuário já é membro da banda.'
                    ]
                ]);
            }

            $band->members()->create([
                'user_id' => $user->id,
                'role' => $request->role
            ]);

            return back()->with([
                'flash' => [
                    'success' => 'Membro adicionado com sucesso!'
                ],
                'band' => $band->fresh('members.user')
            ]);
        } catch (\Throwable $e) {
            \Log::error('Erro ao adicionar membro', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'flash' => [
                    'error' => 'Ocorreu um erro ao adicionar o membro.'
                ]
            ]);
        }
    }



    public function update(Request $request, $bandId, $memberId)
    {
        try {
            // Busca a banda e o membro
            $band = Band::findOrFail($bandId);
            $member = BandMember::findOrFail($memberId);
            
            // Verifica se o membro pertence à banda
            if ($member->band_id != $bandId) {
                return back()->withErrors([
                    'flash' => [
                        'error' => 'Membro não pertence a esta banda.'
                    ]
                ]);
            }
            
            // Verifica se o usuário atual tem permissão para atualizar o membro
            $this->authorize('manageMembers', $band);
            
            $request->validate([
                'role' => 'required|in:admin,editor,viewer'
            ]);
            
            // Verifica se está tentando remover o último administrador
            $adminCount = $band->members()->where('role', 'admin')->count();
            $isCurrentAdmin = $member->role === 'admin';
            $isNewRoleAdmin = $request->role === 'admin';
            
            if ($isCurrentAdmin && !$isNewRoleAdmin && $adminCount <= 1) {
                return back()->withErrors([
                    'flash' => [
                        'error' => 'A banda deve ter pelo menos um administrador.'
                    ]
                ]);
            }
            
            // Atualiza o papel do membro
            $member->role = $request->role;
            $member->save();
            
            return back()->with([
                'flash' => [
                    'success' => 'Papel do membro atualizado com sucesso!'
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar papel do membro: ' . $e->getMessage(), [
                'band_id' => $bandId,
                'member_id' => $memberId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'flash' => [
                    'error' => 'Erro ao atualizar papel do membro: ' . $e->getMessage()
                ]
            ]);
        }
    }

    public function destroy(Band $band, BandMember $member)
    {
        //$this->authorize('manageMembers', $band);
    
        if ($member->role === 'admin') {
            return back()->withErrors([
                'flash' => [
                    'error' => 'Não é possível remover um administrador.'
                ]
            ]);
        }
    
        $member->delete();
    
        return back()->with([
            'flash' => [
                'success' => 'Membro removido com sucesso!'
            ]
        ]);
    }
    
}
