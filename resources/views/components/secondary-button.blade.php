<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-[--teal-light] border-2 border-[--teal-light] rounded-lg font-semibold text-sm text-[--teal-dark] hover:bg-[--teal] hover:border-[--teal] hover:text-white hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-[--teal] focus:ring-offset-2 active:translate-y-0 disabled:opacity-50 disabled:pointer-events-none disabled:hover:translate-y-0 transition-all duration-150']) }}>
 {{ $slot }}
</button>
