@extends('admin.layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @livewire('admin.users.index')
        </div>
    </div>
</div>
@endsection
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucun utilisateur trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endpush
