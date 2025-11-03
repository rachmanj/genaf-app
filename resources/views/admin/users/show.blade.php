<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}
            </h2>
            <div class="flex space-x-2">
                @can('edit', $user)
                    <a href="{{ route('users.edit', $user) }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit User
                    </a>
                @endcan
                <a href="{{ route('users.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Users
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- User Information Card -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Username</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->username ?? 'Not provided' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">NIK</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->nik ?? 'Not provided' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Department</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $user->department?->department_name ?? 'Not assigned' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Role and Status Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Role & Status</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Role(s)</label>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        @forelse($user->roles as $role)
                                            <span
                                                class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                                @if ($role->name === 'admin') bg-red-100 text-red-800
                                                @elseif($role->name === 'manager') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @empty
                                            <span class="text-gray-500">No roles assigned</span>
                                        @endforelse
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span
                                        class="mt-1 inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                        {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Member Since</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Information -->
                    <div class="mt-6 bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Permissions</h3>

                        @if ($user->permissions->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach ($user->getAllPermissions() as $permission)
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No direct permissions assigned (permissions inherited from roles)
                            </p>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex justify-between items-center">
                        <div>
                            @can('delete', $user)
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Delete User
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </div>

                        @can('edit', $user)
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                        {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
