<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-red-600 border-2 border-red-600 rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-red-700 hover:border-red-700 hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:translate-y-0 disabled:opacity-50 disabled:pointer-events-none disabled:hover:translate-y-0 transition-all duration-150']) }}>
 {{ $slot }}
</button>
