<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Borrow
        </h2>
    </x-slot>

    <div>
        <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8">
                <a href="{{ url()->previous() }}" class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded">Back to list</a>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                @foreach ($inventories as $inventory)
                <form method="POST" action="{{ route('book', $inventory->id) }}">
                    @csrf
                    <div class="shadow overflow-hidden sm:rounded-md">

                        <div class="px-4 py-5 bg-white sm:p-6">
                            <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                            <input type="text" name="name" id="name" type="text" class="form-input rounded-md shadow-sm mt-1 block w-full disabled:opacity-50"
                                   value="{{ old('name', $inventory->name) }}" readonly/>
                            @error('name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="px-4 py-5 bg-white sm:p-6">
                            <label for="model" class="block font-medium text-sm text-gray-700">Model</label>
                            <input type="text" name="model" id="model" type="text" class="form-input rounded-md shadow-sm mt-1 block w-full disabled:opacity-50"
                                   value="{{ old('model', $inventory->model) }}" readonly/>
                            @error('model')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="px-4 py-5 bg-white sm:p-6">
                            <label for="serial" class="block font-medium text-sm text-gray-700">Serial Number</label>
                            <input type="text" name="serial" id="serial" type="text" class="form-input rounded-md shadow-sm mt-1 block w-full disabled:opacity-50"
                                   value="{{ old('serial', $inventory->serial) }}" readonly/>
                            @error('serial')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="px-4 py-5 bg-white sm:p-6">
                            <label for="borrow" class="block font-medium text-sm text-gray-700">Borrower</label>
                            <div class="mt-1">
                                <select name="borrow" id="borrow" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                    <option value="" selected></option>
                                    @foreach($users as $user)
                                        <option value="{{$user->name}}">{{$user->name}}</option>
                                    @endforeach
                                    @foreach($guests as $guest)
                                        <option value="{{$guest->name}}">{{$guest->name}}</option>
                                    @endforeach
                                </select>
                                @error('borrow')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                                Borrow
                            </button>
                        </div>
                    </div>
                </form>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>