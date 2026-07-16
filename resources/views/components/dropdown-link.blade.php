<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-800 hover:bg-green-600 hover:text-white focus:outline-none focus:bg-green-600 focus:text-white transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</a>