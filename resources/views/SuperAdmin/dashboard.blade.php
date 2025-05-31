<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                Tableau de bord Super Admin
                <br>
            </div>
        </div>
    </div>

    <div id='super_admin_dashboard'>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Liste des clubs
                    <br>
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (empty($clubs))
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            Aucun club n'a été créé.
                        </div>
                    @else
                        @foreach($clubs as $club)
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <a href="#">{{ $club->name }}</a>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div>
                    <button 
                        href="{{ url('/club/create') }}" 
                        hx-trigger="click"
                        hx-get="/club/create"
                        hx-target="#super_admin_dashboard" 
                        hx-swap="innerHTML"
                        class="hover:bg-gray-100 transition border bg-[#22567d] text-white hover:text-[#22567d] font-bold py-2 px-4 rounded"
                        > Créer un club
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
