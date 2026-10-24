@props(['disabled' => false, 'readonly' => false])

<input @disabled($disabled) @readonly($readonly) {{ $attributes->merge(['class' => 'border-2 border-gray-200 focus:border-[--teal] focus:ring-[--teal] rounded-lg shadow-sm transition-colors duration-150']) }}>
