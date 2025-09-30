<!-- Form Select Dropdown -->
<select name="quantity" 
    class="relative z-10 max-w-full rounded-md border border-gray-300 py-1.5 text-left text-base font-medium leading-5 text-gray-700 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 sm:text-sm"
    x-data
    x-on:click.away="$el.size = 1">
    // ...existing options...
</select>