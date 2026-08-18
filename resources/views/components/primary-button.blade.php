<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-[--teal] border-2 border-[--teal] rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-[--teal-dark] hover:border-[--teal-dark] hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-[--teal] focus:ring-offset-2 active:translate-y-0 active:shadow-sm disabled:opacity-50 disabled:pointer-events-none disabled:hover:translate-y-0 transition-all duration-150']) }}>
 {{ $slot }}
</button>
