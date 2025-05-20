<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full bg-primary-500 hover:bg-green-800 text-white font-semibold py-2 rounded-md shadow transition duration-150']) }}>
    {{ $slot }}
</button>
