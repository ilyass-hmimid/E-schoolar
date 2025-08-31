@extends('layouts.admin')

@section('content')
    @livewire('admin.users.show-user', ['userId' => $user->id])
@endsection
