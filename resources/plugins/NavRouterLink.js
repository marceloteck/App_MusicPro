
const navoffRepeat = {
    inicio: { title: 'Inicio', route: 'index.home', icon: 'home' },
    mais: { title: 'Mais', route: 'more.index', icon: 'more_horiz' }
};

const NavRouterIndex = [
    navoffRepeat.inicio,
    { title: 'Banda', route: 'bands.index', icon: 'speaker_group' },
    { title: 'Buscar', route: 'search.index', icon: 'search' },
    { title: 'Player', route: 'player.index', icon: 'playlist_play' },
    navoffRepeat.mais
];


const NavRouterSubPage = [
    { title: 'Perfil', route: 'profile.edit', icon: 'person' },
    { title: 'Configurações', route: 'settings.index', icon: 'manufacturing' },
    { title: 'Favoritos', route: 'settings.favorites', icon: 'hotel_class' },
    navoffRepeat.mais,
];

const NavRouterPlayer = [
    navoffRepeat.inicio,
    { title: 'Player', route: 'player.index', icon: 'playlist_play' },
    { title: 'Buscar', route: 'player.search', icon: 'search' },
    { title: 'Biblioteca', route: 'library.index', icon: 'library_music' },
    navoffRepeat.mais,
];

export { NavRouterIndex, NavRouterSubPage, NavRouterPlayer };