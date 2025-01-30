<template>

    <footer class="bg-white py-5 mt-8 border-t border-gray-200">
        <p class="text-center text-xs leading-6 text-gray-600">© 2024 All rights reserved.
        </p>
        <div
            class="mt-2 flex items-center justify-center space-x-4 text-xs font-medium leading-6 text-gray-600 hover:text-gray-500 underline underline-offset-2">
            <Link href="/privacy-policy">Privacy policy</Link>
            <div class="h-4 w-px bg-slate-500/20"></div>
            <Link href="/privacy-policy">Terms</Link>
            <div class="h-4 w-px bg-slate-500/20"></div>
            <Link :href="route('public.welcome')">Main site</Link>
        </div>
    </footer>

</template>
