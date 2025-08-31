<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Préférences de notification') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('notification-preferences.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="email" name="email" type="checkbox" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    {{ $preferences['email'] ?? true ? 'checked' : '' }}>
                                <label for="email" class="ml-2 block text-sm text-gray-900">
                                    Recevoir des notifications par email
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="database" name="database" type="checkbox" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    {{ $preferences['database'] ?? true ? 'checked' : '' }}>
                                <label for="database" class="ml-2 block text-sm text-gray-900">
                                    Afficher les notifications dans l'application
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="sms" name="sms" type="checkbox" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    {{ $preferences['sms'] ?? false ? 'checked' : '' }}>
                                <label for="sms" class="ml-2 block text-sm text-gray-900">
                                    Recevoir des notifications par SMS
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="push" name="push" type="checkbox" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    {{ $preferences['push'] ?? false ? 'checked' : '' }}>
                                <label for="push" class="ml-2 block text-sm text-gray-900">
                                    Activer les notifications push
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Enregistrer les préférences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
