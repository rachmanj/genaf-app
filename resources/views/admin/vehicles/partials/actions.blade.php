<div class="btn-group" role="group">
    @can('edit vehicles')
        <a href="{{ route('vehicles.edit', $v->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
    @endcan
    <a href="{{ route('vehicles.show', $v->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
    @can('delete vehicles')
        <form action="{{ route('vehicles.destroy', $v->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this vehicle?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
        </form>
    @endcan
</div>

