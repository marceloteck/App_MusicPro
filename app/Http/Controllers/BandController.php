<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Models\Playlist;
use App\Models\User;
use App\Models\BandMember;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class BandController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Busca apenas as bandas onde o usuário é membro
        $bands = Band::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('members.user')->get();
        
        $flash = session('flash');
        
        return Inertia::render('Pages/Bands/Index', [
            'bands' => $bands,
            'userLists' => Playlist::where('user_id', $user->id)
                                 ->with('songs')
                                 ->get(),
            'flash' => $flash
        ]);
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Iniciando criação de banda', $request->all());

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'genre' => 'required|string|max:100',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            \Log::info('Validação passou');

            $band = new Band();
            $band->name = $request->name;
            $band->description = $request->description;
            $band->genre = $request->genre;

            if ($request->hasFile('image')) {
                \Log::info('Processando upload de imagem');
                $image = $request->file('image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('public/bands', $filename);
                $band->image = Storage::url($path);
                \Log::info('Imagem salva em: ' . $path);
            }

            $band->save();
            \Log::info('Banda salva com ID: ' . $band->id);

            // Adiciona o usuário atual como admin da banda
            $band->members()->create([
                'user_id' => auth()->id(),
                'role' => 'admin'
            ]);
            \Log::info('Usuário adicionado como admin');

            return redirect()->back()->with('success', 'Banda criada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar banda: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Erro ao criar banda: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Busca a banda pelo ID
            $band = Band::findOrFail($id);
            
            \Log::info('Tentando atualizar banda', [
                'band_id' => $band->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            // Verifica autorização
            //$this->authorize('update', $band);

            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'genre'       => 'required|string|max:100',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $this->handleImageUpload($request, $band);
            }

            // Atualiza a banda no banco de dados
            $band->fill($validated);
            $band->save();

            \Log::info('Banda atualizada com sucesso', [
                'band_id' => $band->id,
                'updated_data' => $validated
            ]);

            // Retorna para a página com os dados atualizados
            return back()->with([
                'flash' => [
                    'success' => 'Banda atualizada com sucesso!'
                ],
                'band' => $band->fresh(['members.user'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar banda: ' . $e->getMessage(), [
                'band_id' => $id,
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'flash' => [
                    'error' => 'Erro ao atualizar banda: ' . $e->getMessage()
                ]
            ]);
        }
    }

    private function handleImageUpload(Request $request, Band $band)
    {
        if ($band->image) {
            Storage::delete(str_replace('/storage', 'public', $band->image));
        }

        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('public/bands', $filename);

        return Storage::url($path);
    }

    public function destroy($id)
    {
        try {
            // Busca a banda pelo ID
            $band = Band::findOrFail($id);
            
            \Log::info('Tentando excluir banda', [
                'band_id' => $band->id,
                'user_id' => auth()->id()
            ]);

            // Verifica autorização
            //$this->authorize('delete', $band);

            // Remove a imagem se existir
            if ($band->image) {
                Storage::delete(str_replace('/storage', 'public', $band->image));
            }

            // Remove os membros da banda
            $band->members()->delete();

            // Remove a banda
            $band->delete();

            \Log::info('Banda excluída com sucesso', [
                'band_id' => $id
            ]);

            return redirect()->route('bands.index')->with([
                'flash' => [
                    'success' => 'Banda excluída com sucesso!'
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao excluir banda: ' . $e->getMessage(), [
                'band_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'flash' => [
                    'error' => 'Erro ao excluir banda: ' . $e->getMessage()
                ]
            ]);
        }
    }

    public function transfer(Request $request, Band $band)
    {
        $this->authorize('transfer', $band);

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // Verifica se o usuário é membro da banda
        $newAdmin = $band->members()->where('user_id', $request->user_id)->first();
        if (!$newAdmin) {
            return redirect()->back()->with([
                'flash' => [
                    'error' => 'Usuário não é membro desta banda.'
                ]
            ]);
        }

        // Atualiza o papel do novo admin
        $newAdmin->update(['role' => 'admin']);

        // Atualiza o papel do admin atual para editor
        $currentAdmin = $band->members()->where('user_id', auth()->id())->first();
        $currentAdmin->update(['role' => 'editor']);

        return redirect()->back()->with([
            'flash' => [
                'success' => 'Liderança da banda transferida com sucesso!'
            ]
        ]);
    }

    public function show(Band $band)
    {
        $user = auth()->user();
        $isMember = $band->members()->where('user_id', $user->id)->exists();
        
        if (!$isMember) {
            return redirect()->route('bands.index')
                ->with('flash', ['error' => 'Você não tem permissão para acessar esta banda.']);
        }

        return Inertia::render('Pages/Bands/Show', [
            'band' => $band->load('members.user', 'events')
        ]);
    }

    public function leave($id)
    {
        try {
            // Busca a banda pelo ID
            $band = Band::findOrFail($id);
            
            \Log::info('Tentando sair da banda', [
                'band_id' => $band->id,
                'user_id' => auth()->id()
            ]);

            // Verifica se o usuário é membro da banda
            $member = $band->members()->where('user_id', auth()->id())->first();
            
            if (!$member) {
                return redirect()->route('bands.index')->with([
                    'flash' => [
                        'error' => 'Você não é membro desta banda.'
                    ]
                ]);
            }
            
            // Verifica se o usuário é o último administrador
            if ($member->role === 'admin') {
                $adminCount = $band->members()->where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    return redirect()->route('bands.index')->with([
                        'flash' => [
                            'error' => 'Você é o último administrador da banda. Não é possível sair.'
                        ]
                    ]);
                }
            }
            
            // Remove o usuário da banda
            $member->delete();
            
            \Log::info('Usuário saiu da banda com sucesso', [
                'band_id' => $band->id,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('bands.index')->with([
                'flash' => [
                    'success' => 'Você saiu da banda com sucesso!'
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao sair da banda: ' . $e->getMessage(), [
                'band_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('bands.index')->with([
                'flash' => [
                    'error' => 'Erro ao sair da banda: ' . $e->getMessage()
                ]
            ]);
        }
    }
}
