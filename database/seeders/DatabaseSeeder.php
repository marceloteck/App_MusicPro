<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Event;
use App\Models\Song;
use App\Models\EventSong;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Criar um usuário de teste
        $user = User::factory()->create(['email' => 'teste@email.com']);

        // Criar uma banda
        $band = Band::create([
            'name' => 'Minha Banda',
            'owner_id' => $user->id // Adicionando o dono da banda
        ]);

        // Adicionar o usuário como Admin na banda
        $bandMember = BandMember::create([
            'band_id' => $band->id,
            'user_id' => $user->id,
            'role' => 'admin'
        ]);

        // Criar um evento para a banda
        $event = Event::create([
            'band_id' => $band->id,
            'name' => 'Show de Sábado',
            'date' => now()->addDays(7)
        ]);

        // Criar músicas
        $song1 = Song::create([
            'title' => 'Música 1',
            'artist' => 'Artista 1',
            'lyrics' => 'Letra da música 1...',
            'chords' => 'C G Am F',
            'video_url' => 'https://youtube.com/video1'
        ]);

        $song2 = Song::create([
            'title' => 'Música 2',
            'artist' => 'Artista 2',
            'lyrics' => 'Letra da música 2...',
            'chords' => 'D A Bm G',
            'video_url' => 'https://youtube.com/video2'
        ]);

        // Vincular músicas ao evento
        EventSong::create(['event_id' => $event->id, 'song_id' => $song1->id]);
        EventSong::create(['event_id' => $event->id, 'song_id' => $song2->id]);
    }
}
