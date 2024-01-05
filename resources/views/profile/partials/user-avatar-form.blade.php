<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
          user avatar
        </h2>
<img width="100" height="100" class="rounded-full" src="{{ "/storage/$user->avatar" }}" alt="user avatar">
        <p class="mt-1 text-sm text-gray-600">
add or update user avatar        </p>
    </header>

    @if (session('message'))
    <div class="text-red-500">
        {{ session('message') }}
    </div>
@endif

<form method="post" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" >
    @method('patch')
    @csrf

        <div>
            <x-input-label for="avatar" :value="__('avatar')" />
            <x-text-input id="avatar" name="avatar" type="file" class="mt-1 block w-full" :value="old('name', $user->avatar)"  autofocus autocomplete="avatar" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>






        <div class="flex items-center gap-4 mt-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

        </div>
    </form>
</section>
